<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\PostManager;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makePost(array $overrides = []): Post
    {
        return Post::create(array_merge([
            'type' => 'berita',
            'judul' => 'Kegiatan Madrasah',
            'ringkasan' => 'Ringkasan kegiatan madrasah.',
            'isi' => '<p>'.str_repeat('Isi berita madrasah. ', 5).'</p>',
            'kategori' => 'berita',
            'status' => 'draft',
            'sematkan' => false,
            'tanggal_publish' => today()->toDateString(),
        ], $overrides));
    }

    private function isiValid(): string
    {
        return '<p>'.str_repeat('Isi berita madrasah. ', 5).'</p>';
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_post(): void
    {
        $this->get('/admin/posts/berita')->assertRedirect('/admin/login');
        $this->get('/admin/posts/artikel')->assertRedirect('/admin/login');
    }

    public function test_admin_bisa_membuka_halaman_berita_dan_artikel(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/posts/berita')
            ->assertOk()
            ->assertSee('Daftar Berita')
            ->assertSeeLivewire(PostManager::class);

        $this->actingAs($this->admin())
            ->get('/admin/posts/artikel')
            ->assertOk()
            ->assertSee('Daftar Artikel')
            ->assertSeeLivewire(PostManager::class);
    }

    public function test_halaman_berita_hanya_menampilkan_post_berita(): void
    {
        $this->makePost(['judul' => 'Berita Kegiatan', 'type' => 'berita']);
        $this->makePost(['judul' => 'Artikel Opini', 'type' => 'artikel', 'kategori' => null]);

        $this->actingAs($this->admin())
            ->get('/admin/posts/berita')
            ->assertOk()
            ->assertSee('Berita Kegiatan')
            ->assertDontSee('Artikel Opini');
    }

    public function test_halaman_artikel_hanya_menampilkan_post_artikel(): void
    {
        $this->makePost(['judul' => 'Berita Kegiatan', 'type' => 'berita']);
        $this->makePost(['judul' => 'Artikel Opini', 'type' => 'artikel', 'kategori' => null]);

        $this->actingAs($this->admin())
            ->get('/admin/posts/artikel')
            ->assertOk()
            ->assertSee('Artikel Opini')
            ->assertDontSee('Berita Kegiatan');
    }

    public function test_form_create_di_halaman_berita_tidak_menampilkan_pilihan_tipe(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class, ['type' => 'berita'])
            ->call('create')
            ->assertSet('pageType', 'berita')
            ->assertSet('type', 'berita')
            ->assertSee('Jenis mengikuti halaman admin yang sedang dibuka.')
            ->assertDontSeeHtml('id="type"');
    }

    public function test_form_create_di_halaman_artikel_tidak_menampilkan_pilihan_tipe(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class, ['type' => 'artikel'])
            ->call('create')
            ->assertSet('pageType', 'artikel')
            ->assertSet('type', 'artikel')
            ->assertSee('Jenis mengikuti halaman admin yang sedang dibuka.')
            ->assertDontSeeHtml('id="type"');
    }

    public function test_post_baru_di_halaman_artikel_otomatis_bertipe_artikel(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class, ['type' => 'artikel'])
            ->call('create')
            ->set('judul', 'Gagasan Baru Madrasah')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', $this->isiValid())
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'judul' => 'Gagasan Baru Madrasah',
            'type' => 'artikel',
            'kategori' => null,
        ]);
    }

    public function test_form_menampilkan_editor_tiptap(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->assertSee('Isi Artikel')
            ->assertSeeHtml('tiptapEditor')
            ->assertSee('Sisipkan gambar')
            ->assertSee('Sematkan di beranda');
    }

    public function test_admin_bisa_mengisi_nama_penulis_manual(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Berita Dengan Penulis Manual')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', $this->isiValid())
            ->set('kategori', 'berita')
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::where('judul', 'Berita Dengan Penulis Manual')->firstOrFail();

        $this->assertSame('Ustadz Ahmad Fauzi', $post->penulis_nama);
        $this->assertSame($admin->id, $post->penulis_id);
    }

    public function test_edit_post_menampilkan_nama_penulis_tersimpan(): void
    {
        $post = $this->makePost(['penulis_nama' => 'Penulis Lama']);

        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('edit', $post->id)
            ->assertSet('penulis_nama', 'Penulis Lama');
    }

    public function test_nama_penulis_wajib_diisi(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Berita Tanpa Penulis')
            ->set('penulis_nama', '   ')
            ->set('isi', $this->isiValid())
            ->set('kategori', 'berita')
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasErrors(['penulis_nama' => 'required']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_gaya_editor_aman_dipertahankan_dan_gaya_berbahaya_dibuang(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Berita Dengan Gaya Editor')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', '<h2 style="text-align: center">Judul Tengah</h2>'
                .'<p style="color: #ff0000; position: fixed; background-image: url(javascript:alert(1))">'
                .str_repeat('Isi berita berwarna. ', 5)
                .'</p>')
            ->set('kategori', 'berita')
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::where('judul', 'Berita Dengan Gaya Editor')->firstOrFail();

        $this->assertStringContainsString('text-align: center', $post->isi);
        $this->assertStringContainsString('color: #ff0000', $post->isi);
        $this->assertStringNotContainsString('position', $post->isi);
        $this->assertStringNotContainsString('background-image', $post->isi);
    }

    public function test_admin_bisa_membuat_post_publish_dengan_sampul(): void
    {
        Storage::fake('public');

        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(PostManager::class)
            ->call('create')
            ->set('type', 'berita')
            ->set('judul', 'Berita Baru Madrasah')
            ->set('ringkasan', 'Ringkasan berita baru.')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', $this->isiValid())
            ->set('kategori', 'berita')
            ->set('status', 'publish')
            ->set('tanggal_publish', today()->toDateString())
            ->set('sampul', UploadedFile::fake()->image('sampul.jpg', 1200, 800)->size(1024))
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertDispatched('toast');

        $post = Post::where('judul', 'Berita Baru Madrasah')->firstOrFail();

        $this->assertSame('berita-baru-madrasah', $post->slug);
        $this->assertSame('publish', $post->status);
        $this->assertSame($admin->id, $post->penulis_id);
        $this->assertTrue($post->hasMedia('sampul'));
        $this->assertCount(1, $post->getMedia('sampul'));
    }

    public function test_post_publish_boleh_tanpa_sampul(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Berita Tanpa Sampul')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', $this->isiValid())
            ->set('kategori', 'berita')
            ->set('status', 'publish')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'judul' => 'Berita Tanpa Sampul',
            'status' => 'publish',
        ]);
    }

    public function test_post_draft_boleh_tanpa_sampul(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Draft Tanpa Sampul')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', $this->isiValid())
            ->set('kategori', 'berita')
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'judul' => 'Draft Tanpa Sampul',
            'status' => 'draft',
        ]);
    }

    public function test_admin_bisa_mengubah_post(): void
    {
        $post = $this->makePost();

        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('edit', $post->id)
            ->assertSet('judul', $post->judul)
            ->set('judul', 'Judul Berita Diperbarui')
            ->set('ringkasan', 'Ringkasan diperbarui.')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'judul' => 'Judul Berita Diperbarui',
            'ringkasan' => 'Ringkasan diperbarui.',
        ]);
    }

    public function test_admin_bisa_menghapus_post(): void
    {
        $post = $this->makePost();

        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('confirmDelete', $post->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete')
            ->assertSet('showDeleteModal', false)
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_hapus_post_membersihkan_gambar_inline(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/inline/contoh.jpg', 'dummy');

        $post = $this->makePost([
            'isi' => '<p>'.str_repeat('Isi berita madrasah. ', 5).'</p>'
                .'<img src="'.Storage::disk('public')->url('posts/inline/contoh.jpg').'" alt="Contoh">',
        ]);

        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('confirmDelete', $post->id)
            ->call('delete')
            ->assertDispatched('toast');

        Storage::disk('public')->assertMissing('posts/inline/contoh.jpg');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_filter_dan_pencarian_bisa_dikombinasikan(): void
    {
        $this->makePost(['judul' => 'Berita Kegiatan', 'type' => 'berita', 'status' => 'draft']);
        $this->makePost(['judul' => 'Artikel Opini', 'type' => 'artikel', 'status' => 'publish', 'kategori' => null]);

        Livewire::actingAs($this->admin())
            ->test(PostManager::class, ['type' => 'artikel'])
            ->assertSee('Artikel Opini')
            ->assertDontSee('Berita Kegiatan')
            ->set('filterStatus', 'publish')
            ->assertSee('Artikel Opini')
            ->set('search', 'Opini')
            ->assertSee('Artikel Opini');
    }

    public function test_isi_berbahaya_dibersihkan_saat_disimpan(): void
    {
        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Berita Aman')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', '<p>'.str_repeat('Isi aman. ', 10).'</p>'
                .'<script>alert("xss")</script>'
                .'<img src="javascript:alert(1)" onerror="alert(1)">'
                .'<p onclick="alert(1)">Klik</p>')
            ->set('kategori', 'berita')
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::where('judul', 'Berita Aman')->firstOrFail();

        $this->assertStringContainsString('<p>', $post->isi);
        $this->assertStringNotContainsString('<script', $post->isi);
        $this->assertStringNotContainsString('onerror', $post->isi);
        $this->assertStringNotContainsString('javascript:', $post->isi);
        $this->assertStringNotContainsString('onclick', $post->isi);
    }

    public function test_slug_otomatis_tidak_duplikat(): void
    {
        $first = $this->makePost(['judul' => 'Kegiatan Madrasah']);
        $second = $this->makePost(['judul' => 'Kegiatan Madrasah']);

        $this->assertSame('kegiatan-madrasah', $first->slug);
        $this->assertSame('kegiatan-madrasah-1', $second->slug);
    }

    public function test_slug_manual_duplikat_mendapat_akhiran_angka(): void
    {
        $this->makePost(['judul' => 'Post Pertama', 'slug' => 'slug-sama']);

        Livewire::actingAs($this->admin())
            ->test(PostManager::class)
            ->call('create')
            ->set('judul', 'Post Kedua')
            ->set('slug', 'slug-sama')
            ->set('penulis_nama', 'Ustadz Ahmad Fauzi')
            ->set('isi', $this->isiValid())
            ->set('kategori', 'berita')
            ->set('status', 'draft')
            ->set('tanggal_publish', today()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'judul' => 'Post Kedua',
            'slug' => 'slug-sama-1',
        ]);
    }

    public function test_admin_bisa_mengunggah_gambar_inline(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin())
            ->postJson(route('admin.posts.inline-image'), [
                'image' => UploadedFile::fake()->image('inline.jpg', 800, 600)->size(1024),
            ]);

        $response->assertOk()->assertJsonStructure(['url', 'path']);

        $this->assertTrue(Storage::disk('public')->exists($response->json('path')));
    }
}
