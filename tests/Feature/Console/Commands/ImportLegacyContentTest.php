<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImportLegacyContentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Folder sumber sementara yang harus dibersihkan setelah test.
     *
     * @var list<string>
     */
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            File::deleteDirectory($directory);
        }

        parent::tearDown();
    }

    public function test_perintah_mengimpor_semua_koleksi_beserta_media(): void
    {
        Storage::fake('public');

        $source = $this->makeLegacySite();

        $this->artisan('import:legacy-content', ['--source' => $source])
            ->expectsOutputToContain('Ringkasan impor')
            ->expectsOutputToContain('media baru: 3')
            ->assertSuccessful();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('galleries', 1);
        $this->assertDatabaseCount('gallery_images', 2);
        $this->assertDatabaseCount('achievements', 2);
        $this->assertDatabaseCount('media', 3);

        $post = Post::query()->where('slug', 'upacara-sumpah-pemuda')->firstOrFail();

        $this->assertSame('Upacara Hari Sumpah Pemuda', $post->judul);
        $this->assertSame('berita', $post->type);
        $this->assertSame('kegiatan', $post->kategori);
        $this->assertSame('publish', $post->status);
        $this->assertTrue($post->sematkan);
        $this->assertSame('2026-10-28', $post->tanggal_publish->toDateString());
        $this->assertSame('Upacara "khidmat" berlangsung lancar.', $post->ringkasan);
        $this->assertStringContainsString('Lihat postingan Instagram', $post->isi);
        $this->assertStringContainsString('https://www.instagram.com/reel/abc123/', $post->isi);

        $postCover = $post->getFirstMedia('sampul');

        $this->assertNotNull($postCover);
        $this->assertSame('upacara.jpg', $postCover->file_name);
        Storage::disk('public')->assertExists($postCover->getPathRelativeToRoot());

        $gallery = Gallery::query()->where('album', 'Upacara Hari Senin')->firstOrFail();

        $this->assertSame('Dokumentasi upacara bendera.', $gallery->keterangan);

        $images = $gallery->images()->get();

        $this->assertSame([1, 2], $images->pluck('urutan')->all());
        $this->assertSame(['01.jpg', '02.jpg'], $images->map(
            fn (GalleryImage $image): ?string => $image->getFirstMedia('foto')?->file_name,
        )->all());

        foreach ($images as $image) {
            $photo = $image->getFirstMedia('foto');

            $this->assertNotNull($photo);
            Storage::disk('public')->assertExists($photo->getPathRelativeToRoot());
        }

        $this->assertDatabaseHas('achievements', [
            'judul' => 'Juara 1 Kaligrafi Tingkat Kabupaten',
            'tahun' => 2025,
            'penyelenggara' => 'PORSEMA Kabupaten',
        ]);
        $this->assertDatabaseHas('achievements', [
            'judul' => 'Juara 1 Kaligrafi Tingkat Kabupaten',
            'tahun' => 2025,
            'penyelenggara' => 'PORSENI Kabupaten',
        ]);

        $this->assertFileExists($source.'/public/images/berita/upacara.jpg');
        $this->assertFileExists($source.'/public/images/galeri/upacara/01.jpg');
        $this->assertFileExists($source.'/public/images/galeri/upacara/02.jpg');
    }

    public function test_perintah_idempotent_saat_dijalankan_ulang(): void
    {
        Storage::fake('public');

        $source = $this->makeLegacySite();

        $this->artisan('import:legacy-content', ['--source' => $source])
            ->assertSuccessful();

        $postId = Post::query()->value('id');
        $galleryId = Gallery::query()->value('id');

        $this->artisan('import:legacy-content', ['--source' => $source])
            ->expectsOutputToContain('diperbarui')
            ->expectsOutputToContain('media baru: 0')
            ->assertSuccessful();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('galleries', 1);
        $this->assertDatabaseCount('gallery_images', 2);
        $this->assertDatabaseCount('achievements', 2);
        $this->assertDatabaseCount('media', 3);

        $this->assertSame($postId, Post::query()->value('id'));
        $this->assertSame($galleryId, Gallery::query()->value('id'));
        $this->assertSame(1, Post::query()->firstOrFail()->media()->count());
        $this->assertSame(2, Gallery::query()->firstOrFail()->images()->count());
    }

    public function test_file_rusak_dilaporkan_tanpa_menghentikan_file_lain(): void
    {
        Storage::fake('public');

        $source = $this->makeLegacySite();

        $this->writeMarkdown($source.'/src/content/berita/02-rusak.md', <<<'MARKDOWN'
---
judul: "Berita Rusak
tanggal: 2026-10-01
kategori: berita
---
MARKDOWN);

        $this->artisan('import:legacy-content', ['--source' => $source])
            ->expectsOutputToContain('02-rusak.md')
            ->expectsOutputToContain('File yang gagal diproses')
            ->assertExitCode(Command::FAILURE);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('galleries', 1);
        $this->assertDatabaseCount('achievements', 2);
        $this->assertDatabaseHas('posts', [
            'slug' => 'upacara-sumpah-pemuda',
        ]);
        $this->assertDatabaseMissing('posts', [
            'judul' => 'Berita Rusak',
        ]);
    }

    public function test_dry_run_tidak_menulis_data_maupun_media(): void
    {
        Storage::fake('public');

        $source = $this->makeLegacySite();

        $this->artisan('import:legacy-content', [
            '--source' => $source,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('Mode dry-run')
            ->expectsOutputToContain('Dry-run selesai')
            ->assertSuccessful();

        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('galleries', 0);
        $this->assertDatabaseCount('gallery_images', 0);
        $this->assertDatabaseCount('achievements', 0);
        $this->assertDatabaseCount('media', 0);
    }

    /**
     * Buat struktur arsip Astro minimal untuk test.
     */
    private function makeLegacySite(): string
    {
        $source = storage_path('framework/testing/legacy-import-'.Str::uuid());
        $this->temporaryDirectories[] = $source;

        $this->writeMarkdown($source.'/src/content/berita/01-upacara-sumpah-pemuda.md', <<<'MARKDOWN'
---
judul: "Upacara Hari Sumpah Pemuda"
tanggal: 2026-10-28
kategori: kegiatan
ringkasan: 'Upacara "khidmat" berlangsung lancar.'
instagramUrl: "https://www.instagram.com/reel/abc123/"
sampul: "/images/berita/upacara.jpg"
sematkan: true
---
MARKDOWN);

        $this->writeMarkdown($source.'/src/content/galeri/01-upacara-hari-senin.md', <<<'MARKDOWN'
---
album: "Upacara Hari Senin"
keterangan: "Dokumentasi upacara bendera."
foto:
  - "/images/galeri/upacara/01.jpg"
  - "/images/galeri/upacara/02.jpg"
urutan: 1
---
MARKDOWN);

        $this->writeMarkdown($source.'/src/content/achievement/01-kaligrafi-porsema.md', <<<'MARKDOWN'
---
judul: "Juara 1 Kaligrafi Tingkat Kabupaten"
peringkat: "Juara 1"
tingkat: "kabupaten"
bidang: "seni"
tahun: 2025
penyelenggara: "PORSEMA Kabupaten"
urutan: 1
---
MARKDOWN);

        $this->writeMarkdown($source.'/src/content/achievement/02-kaligrafi-porseni.md', <<<'MARKDOWN'
---
judul: "Juara 1 Kaligrafi Tingkat Kabupaten"
peringkat: "Juara 1"
tingkat: "kabupaten"
bidang: "seni"
tahun: 2025
penyelenggara: "PORSENI Kabupaten"
urutan: 2
---
MARKDOWN);

        $this->createImage($source.'/public/images/berita/upacara.jpg');
        $this->createImage($source.'/public/images/galeri/upacara/01.jpg');
        $this->createImage($source.'/public/images/galeri/upacara/02.jpg');

        return $source;
    }

    private function writeMarkdown(string $path, string $contents): void
    {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);
    }

    private function createImage(string $path): void
    {
        File::ensureDirectoryExists(dirname($path));

        UploadedFile::fake()
            ->image(basename($path), 1200, 800)
            ->move(dirname($path), basename($path));
    }
}
