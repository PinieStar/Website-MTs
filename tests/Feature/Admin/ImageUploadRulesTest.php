<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\GalleryManager;
use App\Livewire\Admin\PostManager;
use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class ImageUploadRulesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function postForm(?UploadedFile $sampul): Testable
    {
        return Livewire::actingAs($this->admin())
            ->test(PostManager::class, ['type' => 'berita'])
            ->call('create')
            ->set('judul', 'Berita Uji Gambar')
            ->set('ringkasan', 'Ringkasan.')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', '<p>'.str_repeat('Isi berita uji gambar. ', 5).'</p>')
            ->set('kategori', 'berita')
            ->set('status', 'publish')
            ->set('tanggal_publish', today()->toDateString())
            ->set('sampul', $sampul);
    }

    public function test_sampul_png_ditolak(): void
    {
        Storage::fake('public');

        $this->postForm(UploadedFile::fake()->image('sampul.png', 800, 600)->size(300))
            ->call('save')
            ->assertHasErrors(['sampul' => 'mimes']);
    }

    public function test_sampul_lebih_dari_2mb_ditolak(): void
    {
        Storage::fake('public');

        $this->postForm(UploadedFile::fake()->image('sampul.jpg', 800, 600)->size(2049))
            ->call('save')
            ->assertHasErrors(['sampul' => 'max']);
    }

    public function test_sampul_jpg_tepat_2mb_diterima(): void
    {
        Storage::fake('public');

        $this->postForm(UploadedFile::fake()->image('sampul.jpg', 800, 600)->size(2048))
            ->call('save')
            ->assertHasNoErrors();
    }

    public function test_gambar_inline_png_atau_lebih_dari_2mb_ditolak(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->postJson(route('admin.posts.inline-image'), [
                'image' => UploadedFile::fake()->image('inline.png', 800, 600)->size(300),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('image');

        $this->actingAs($admin)
            ->postJson(route('admin.posts.inline-image'), [
                'image' => UploadedFile::fake()->image('inline.jpg', 800, 600)->size(2049),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('image');
    }

    public function test_foto_galeri_png_atau_lebih_dari_2mb_ditolak(): void
    {
        Storage::fake('public');

        $gallery = Gallery::create(['album' => 'Album Uji', 'keterangan' => 'Uji aturan gambar.']);

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class, ['galleryId' => $gallery->id])
            ->set('photos', [
                UploadedFile::fake()->image('foto.png', 800, 600)->size(300),
                UploadedFile::fake()->image('besar.jpg', 800, 600)->size(2049),
                UploadedFile::fake()->image('pas.jpg', 800, 600)->size(2048),
            ])
            ->call('uploadPhotos')
            ->assertHasErrors(['photos.0', 'photos.1'])
            ->assertHasNoErrors(['photos.2']);

        $this->assertDatabaseCount('gallery_images', 0);
    }
}
