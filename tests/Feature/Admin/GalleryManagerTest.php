<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\GalleryManager;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GalleryManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeGallery(array $overrides = []): Gallery
    {
        return Gallery::create(array_merge([
            'album' => 'Kegiatan Madrasah',
            'keterangan' => 'Dokumentasi kegiatan madrasah.',
        ], $overrides));
    }

    private function addImage(Gallery $gallery, string $name = 'foto.jpg', int $urutan = 1): GalleryImage
    {
        $image = $gallery->images()->create([
            'urutan' => $urutan,
        ]);

        $image->addMedia(UploadedFile::fake()->image($name, 1200, 800)->size(512))
            ->toMediaCollection('foto');

        return $image;
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_galeri(): void
    {
        $this->get('/admin/galleries')->assertRedirect('/admin/login');
    }

    public function test_admin_bisa_membuka_halaman_galeri(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/galleries')
            ->assertOk()
            ->assertSee('Daftar Album Galeri')
            ->assertSeeLivewire(GalleryManager::class);
    }

    public function test_admin_bisa_membuka_halaman_kelola_foto_album(): void
    {
        $gallery = $this->makeGallery();

        $this->actingAs($this->admin())
            ->get('/admin/galleries/'.$gallery->id)
            ->assertOk()
            ->assertSee('Unggah Foto')
            ->assertSeeLivewire(GalleryManager::class);
    }

    public function test_admin_bisa_membuat_album(): void
    {
        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class)
            ->call('createAlbum')
            ->set('album', 'Upacara Hari Senin')
            ->set('keterangan', 'Upacara bendera rutin setiap hari Senin.')
            ->call('saveAlbum')
            ->assertHasNoErrors()
            ->assertSet('showAlbumForm', false)
            ->assertDispatched('toast');

        $gallery = Gallery::where('album', 'Upacara Hari Senin')->firstOrFail();

        $this->assertSame('Upacara bendera rutin setiap hari Senin.', $gallery->keterangan);
    }

    public function test_album_wajib_memiliki_nama(): void
    {
        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class)
            ->call('createAlbum')
            ->set('album', '')
            ->call('saveAlbum')
            ->assertHasErrors(['album' => 'required']);

        $this->assertDatabaseCount('galleries', 0);
    }

    public function test_admin_bisa_mengubah_album(): void
    {
        $gallery = $this->makeGallery();

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class)
            ->call('editAlbum', $gallery->id)
            ->set('album', 'Kegiatan Madrasah Diperbarui')
            ->set('keterangan', 'Keterangan baru.')
            ->call('saveAlbum')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $gallery->refresh();

        $this->assertSame('Kegiatan Madrasah Diperbarui', $gallery->album);
        $this->assertSame('Keterangan baru.', $gallery->keterangan);
    }

    public function test_daftar_album_menampilkan_album_terbaru_di_atas(): void
    {
        $lama = $this->makeGallery(['album' => 'Album Lama']);
        $lama->forceFill(['created_at' => now()->subDay()])->save();

        $this->makeGallery(['album' => 'Album Baru']);

        $this->actingAs($this->admin())
            ->get('/admin/galleries')
            ->assertOk()
            ->assertSeeInOrder(['Album Baru', 'Album Lama']);
    }

    public function test_daftar_album_menampilkan_jumlah_foto(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();
        $first = $this->addImage($gallery, 'foto-1.jpg', 1);
        $second = $this->addImage($gallery, 'foto-2.jpg', 2);

        $this->actingAs($this->admin())
            ->get('/admin/galleries')
            ->assertOk()
            ->assertSee('2 Foto')
            ->assertSee($first->getFirstMediaUrl('foto'))
            ->assertDontSee($second->getFirstMediaUrl('foto'));
    }

    public function test_admin_bisa_mengunggah_banyak_foto_sekaligus(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class, ['galleryId' => $gallery->id])
            ->set('photos', [
                UploadedFile::fake()->image('foto-1.jpg', 1200, 800)->size(512),
                UploadedFile::fake()->image('foto-2.jpg', 1200, 800)->size(512),
            ])
            ->call('uploadPhotos')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $gallery->refresh();

        $this->assertCount(2, $gallery->images);
        $this->assertSame([1, 2], $gallery->images->pluck('urutan')->all());

        foreach ($gallery->images as $image) {
            $this->assertTrue($image->hasMedia('foto'));
        }
    }

    public function test_upload_foto_invalid_menampilkan_error_per_file(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class, ['galleryId' => $gallery->id])
            ->set('photos', [
                UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
                UploadedFile::fake()->image('besar.jpg', 1200, 800)->size(2500),
            ])
            ->call('uploadPhotos')
            ->assertHasErrors(['photos.0', 'photos.1'])
            ->assertSee('dokumen.pdf')
            ->assertSee('formatnya tidak didukung')
            ->assertSee('besar.jpg')
            ->assertSee('terlalu besar');

        $this->assertDatabaseCount('gallery_images', 0);
    }

    public function test_admin_bisa_menghapus_foto_tanpa_menghapus_album(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();
        $image = $this->addImage($gallery, 'foto-dihapus.jpg', 1);
        $mediaPath = $image->getFirstMedia('foto')->getPathRelativeToRoot();

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class, ['galleryId' => $gallery->id])
            ->call('confirmDeletePhoto', $image->id)
            ->assertSet('showDeletePhotoModal', true)
            ->call('deletePhoto')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('gallery_images', ['id' => $image->id]);
        $this->assertDatabaseHas('galleries', ['id' => $gallery->id]);
        Storage::disk('public')->assertMissing($mediaPath);
    }

    public function test_admin_bisa_mengubah_urutan_foto(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();
        $first = $this->addImage($gallery, 'foto-1.jpg', 1);
        $second = $this->addImage($gallery, 'foto-2.jpg', 2);
        $third = $this->addImage($gallery, 'foto-3.jpg', 3);

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class, ['galleryId' => $gallery->id])
            ->call('movePhotoDown', $first->id);

        $this->assertSame(
            [$second->id, $first->id, $third->id],
            $gallery->refresh()->images->pluck('id')->all(),
        );
        $this->assertSame([1, 2, 3], $gallery->images->pluck('urutan')->all());
    }

    public function test_hapus_album_menghapus_semua_foto_dan_file(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();
        $first = $this->addImage($gallery, 'foto-1.jpg', 1);
        $second = $this->addImage($gallery, 'foto-2.jpg', 2);

        $paths = [
            $first->getFirstMedia('foto')->getPathRelativeToRoot(),
            $second->getFirstMedia('foto')->getPathRelativeToRoot(),
        ];

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class)
            ->call('confirmDeleteAlbum', $gallery->id)
            ->assertSet('showDeleteAlbumModal', true)
            ->call('deleteAlbum')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
        $this->assertDatabaseCount('gallery_images', 0);

        foreach ($paths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_hapus_album_dari_halaman_kelola_mengarahkan_ke_daftar(): void
    {
        Storage::fake('public');

        $gallery = $this->makeGallery();

        Livewire::actingAs($this->admin())
            ->test(GalleryManager::class, ['galleryId' => $gallery->id])
            ->call('confirmDeleteAlbum', $gallery->id)
            ->call('deleteAlbum')
            ->assertRedirect(route('admin.galleries.index'));

        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }
}
