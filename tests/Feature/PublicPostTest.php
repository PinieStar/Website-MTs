<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\Paginator;
use Tests\TestCase;

class PublicPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makePost(array $overrides = []): Post
    {
        return Post::create(array_merge([
            'type' => 'berita',
            'judul' => 'Berita Madrasah',
            'ringkasan' => 'Ringkasan berita madrasah.',
            'isi' => '<p>Isi lengkap berita madrasah untuk halaman publik.</p>',
            'kategori' => 'berita',
            'status' => 'publish',
            'sematkan' => false,
            'tanggal_publish' => today()->subDay()->toDateString(),
        ], $overrides));
    }

    public function test_halaman_berita_hanya_menampilkan_post_publish_yang_sudah_tayang(): void
    {
        $this->makePost(['judul' => 'Berita Tayang']);
        $this->makePost(['judul' => 'Berita Draft', 'status' => 'draft']);
        $this->makePost(['judul' => 'Berita Masa Depan', 'tanggal_publish' => today()->addDay()->toDateString()]);

        $this->get('/berita')
            ->assertOk()
            ->assertViewIs('public.berita.index')
            ->assertSee('Berita Tayang')
            ->assertDontSee('Berita Draft')
            ->assertDontSee('Berita Masa Depan');
    }

    public function test_halaman_artikel_hanya_menampilkan_artikel(): void
    {
        $this->makePost(['judul' => 'Artikel Opini', 'type' => 'artikel', 'kategori' => null]);
        $this->makePost(['judul' => 'Berita Biasa', 'type' => 'berita']);

        $this->get('/artikel')
            ->assertOk()
            ->assertViewIs('public.artikel.index')
            ->assertSee('Artikel Opini')
            ->assertDontSee('Berita Biasa');
    }

    public function test_detail_post_publish_bisa_dibuka(): void
    {
        $post = $this->makePost(['judul' => 'Berita Detail']);

        $this->get('/berita/'.$post->slug)
            ->assertOk()
            ->assertViewIs('public.berita.show')
            ->assertSee('Berita Detail')
            ->assertSee('Isi lengkap berita madrasah');
    }

    public function test_isi_publik_mempertahankan_paragraf_dan_warna_menyesuaikan_tema(): void
    {
        $post = $this->makePost([
            'isi' => '<p>Paragraf pertama.</p>'
                .'<p><span style="color: #0a0a0a">Paragraf kedua netral.</span></p>'
                .'<p><span style="color: #ff0000">Teks merah.</span></p>',
        ]);

        $this->get('/berita/'.$post->slug)
            ->assertOk()
            ->assertSee('<p>Paragraf pertama.</p><p>', false)
            ->assertSee('Paragraf kedua netral.', false)
            ->assertDontSee('#0a0a0a', false)
            ->assertDontSee('rgb(10, 10, 10)', false)
            ->assertSee('light-dark(', false);
    }

    public function test_detail_post_draft_tidak_bisa_dibuka(): void
    {
        $post = $this->makePost(['judul' => 'Berita Rahasia', 'status' => 'draft']);

        $this->get('/berita/'.$post->slug)->assertNotFound();
    }

    public function test_detail_post_tidak_bisa_dibuka_lewat_tipe_lain(): void
    {
        $post = $this->makePost(['judul' => 'Artikel Lintas Tipe', 'type' => 'artikel', 'kategori' => null]);

        $this->get('/berita/'.$post->slug)->assertNotFound();
        $this->get('/artikel/'.$post->slug)
            ->assertOk()
            ->assertViewIs('public.artikel.show');
    }

    public function test_detail_menampilkan_nama_penulis_manual(): void
    {
        $post = $this->makePost(['penulis_nama' => 'Ustadz Ahmad Fauzi']);

        $this->get('/berita/'.$post->slug)
            ->assertOk()
            ->assertSee('Ustadz Ahmad Fauzi');
    }

    public function test_detail_memakai_nama_akun_jika_penulis_manual_kosong(): void
    {
        $user = User::factory()->create(['name' => 'Admin Redaksi']);
        $post = $this->makePost(['penulis_id' => $user->id, 'penulis_nama' => null]);

        $this->get('/berita/'.$post->slug)
            ->assertOk()
            ->assertSee('Admin Redaksi');
    }

    public function test_detail_tidak_merender_html_berbahaya(): void
    {
        $post = $this->makePost([
            'judul' => 'Berita Aman',
            'isi' => '<p>Isi aman untuk pembaca.</p><script>alert("xss")</script><img src="x" onerror="alert(1)">',
        ]);

        $this->get('/berita/'.$post->slug)
            ->assertOk()
            ->assertSee('Isi aman untuk pembaca.')
            ->assertDontSee('alert("xss")', false)
            ->assertDontSee('onerror', false);
    }

    public function test_halaman_berita_menampilkan_pagination_saat_post_lebih_dari_sembilan(): void
    {
        Paginator::useTailwind();

        foreach (range(1, 10) as $index) {
            $this->makePost(['judul' => 'Berita Pagination '.$index]);
        }

        $this->get('/berita')
            ->assertOk()
            ->assertSee('Menampilkan')
            ->assertSee('Berikutnya')
            ->assertSee('page=2', false);
    }

    public function test_post_tersemat_tampil_lebih_dahulu(): void
    {
        $this->makePost([
            'judul' => 'Berita Biasa',
            'sematkan' => false,
            'tanggal_publish' => today()->toDateString(),
        ]);
        $this->makePost([
            'judul' => 'Berita Unggulan',
            'sematkan' => true,
            'tanggal_publish' => today()->subWeek()->toDateString(),
        ]);

        $this->get('/berita')
            ->assertOk()
            ->assertSeeInOrder(['Berita Unggulan', 'Berita Biasa']);
    }
}
