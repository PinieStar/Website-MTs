<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\GalleryImage;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicGalleryTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_halaman_galeri_menampilkan_album_terbaru_di_atas(): void
    {
        Storage::fake('public');

        $lama = $this->makeGallery(['album' => 'Album Lama']);
        $lama->forceFill(['created_at' => now()->subDay()])->save();

        $baru = $this->makeGallery(['album' => 'Album Baru']);

        $this->addImage($lama, 'foto-lama.jpg', 1);
        $this->addImage($baru, 'foto-baru.jpg', 1);

        $this->get('/galeri')
            ->assertOk()
            ->assertViewIs('public.galeri.index')
            ->assertSeeInOrder(['Album Baru', 'Album Lama']);
    }

    public function test_album_baru_tanpa_foto_tetap_tampil_di_galeri_publik(): void
    {
        $this->makeGallery(['album' => 'Album Baru']);

        $this->get('/galeri')
            ->assertOk()
            ->assertSee('Album Baru')
            ->assertDontSee('0 Foto');
    }

    public function test_setiap_kartu_album_punya_data_lightbox_items_json_valid(): void
    {
        Storage::fake('public');

        $albumDuaFoto = $this->makeGallery(['album' => 'Album Dua Foto']);
        $this->addImage($albumDuaFoto, 'foto-2.jpg', 2);
        $this->addImage($albumDuaFoto, 'foto-1.jpg', 1);

        $albumSatuFoto = $this->makeGallery(['album' => 'Album Satu Foto']);
        $this->addImage($albumSatuFoto, 'foto-tunggal.jpg', 1);

        $response = $this->get('/galeri')
            ->assertOk()
            ->assertSee('data-lightbox-items', false)
            ->assertSee('data-lightbox', false)
            ->assertSee('2 Foto');

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($response->getContent());
        libxml_clear_errors();

        $nodes = (new DOMXPath($dom))->query('//*[@data-lightbox-items]');

        $this->assertNotFalse($nodes);
        $this->assertSame(2, $nodes->length);

        $counts = [];

        foreach ($nodes as $node) {
            $items = json_decode($node->getAttribute('data-lightbox-items'), true, 512, JSON_THROW_ON_ERROR);

            $this->assertIsArray($items);

            foreach ($items as $item) {
                $this->assertSame(['src', 'alt', 'caption'], array_keys($item));
                $this->assertNotSame('', $item['src']);
            }

            $counts[] = count($items);
        }

        sort($counts);

        $this->assertSame([1, 2], $counts);
    }

    public function test_semua_album_tampil_tanpa_pagination(): void
    {
        $albums = [];

        for ($index = 1; $index <= 10; $index++) {
            $albums[] = 'Album Nomor '.$index;
            $this->makeGallery(['album' => 'Album Nomor '.$index]);
        }

        $response = $this->get('/galeri')->assertOk();

        foreach ($albums as $album) {
            $response->assertSee($album);
        }
    }

    public function test_route_detail_album_lama_tidak_ada_lagi(): void
    {
        $gallery = $this->makeGallery();

        $this->get('/galeri/'.$gallery->id)->assertNotFound();
    }
}
