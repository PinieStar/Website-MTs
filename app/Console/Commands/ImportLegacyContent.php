<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Post;
use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use SplFileInfo;
use Throwable;

class ImportLegacyContent extends Command
{
    /**
     * Nama dan urutan koleksi yang diimpor dari arsip Astro.
     *
     * @var array<string, string>
     */
    private const COLLECTIONS = [
        'berita' => 'berita',
        'galeri' => 'galeri',
        'achievement' => 'achievement',
    ];

    /**
     * Label koleksi untuk laporan terminal.
     *
     * @var array<string, string>
     */
    private const LABELS = [
        'berita' => 'Berita',
        'galeri' => 'Galeri',
        'achievement' => 'Prestasi',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:legacy-content
        {--source= : Path folder legacy-astro-site (default: ../legacy-astro-site)}
        {--dry-run : Validasi dan tampilkan hasil tanpa menulis ke database atau media}
        {--force : Lewati konfirmasi saat dijalankan di environment production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor berita, galeri, dan prestasi dari arsip project Astro lama';

    /**
     * Ringkasan hasil per koleksi.
     *
     * @var array<string, array{dibuat: int, diperbarui: int, valid: int, gagal: int}>
     */
    private array $summary = [];

    /**
     * Daftar file yang gagal diproses.
     *
     * @var list<array{file: string, reason: string}>
     */
    private array $failures = [];

    /**
     * Path absolut folder arsip Astro.
     */
    private string $sourcePath = '';

    /**
     * Jumlah file media yang ditambahkan pada proses ini.
     */
    private int $mediaAdded = 0;

    /**
     * Apakah command berjalan tanpa menulis data.
     */
    private bool $dryRun = false;

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $files): int
    {
        $this->dryRun = (bool) $this->option('dry-run');
        $this->resetSummary();

        $sourcePath = $this->resolveSourcePath();

        if ($sourcePath === null) {
            return self::FAILURE;
        }

        $this->sourcePath = $sourcePath;

        if (! $this->dryRun && $this->getLaravel()->environment('production') && ! $this->option('force')) {
            $confirmed = $this->confirm(
                'Command ini akan menulis data ke database PRODUKSI. Lanjutkan?',
                false,
            );

            if (! $confirmed) {
                $this->warn('Impor dibatalkan.');

                return self::FAILURE;
            }
        }

        $this->info("Sumber data: {$this->sourcePath}");

        if ($this->dryRun) {
            $this->warn('Mode dry-run: tidak ada perubahan database atau media.');
        }

        foreach (self::COLLECTIONS as $type => $directory) {
            $contentPath = $this->sourcePath.'/src/content/'.$directory;

            if (! is_dir($contentPath)) {
                $this->error("Folder sumber tidak ditemukan: {$contentPath}");

                return self::FAILURE;
            }

            $filesToImport = $this->markdownFiles($files, $contentPath);
            $label = self::LABELS[$type];

            $this->newLine();
            $this->line("Memproses {$label} dari src/content/{$directory}...");

            if ($filesToImport === []) {
                $this->warn('  Tidak ada file .md.');

                continue;
            }

            foreach ($filesToImport as $file) {
                $relativePath = $type.'/'.$file->getRelativePathname();

                $this->runFile($type, $relativePath, function (bool $write) use ($file, $type): string {
                    return match ($type) {
                        'berita' => $this->importPost($file, $write),
                        'galeri' => $this->importGallery($file, $write),
                        'achievement' => $this->importAchievement($file, $write),
                    };
                });
            }
        }

        $this->renderSummary();

        return $this->failures === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Tentukan path folder arsip Astro yang akan dibaca.
     */
    private function resolveSourcePath(): ?string
    {
        $source = $this->option('source');
        $source = is_string($source) && trim($source) !== ''
            ? $source
            : base_path('../legacy-astro-site');

        $resolved = realpath($source);

        if ($resolved === false || ! is_dir($resolved)) {
            $this->error("Folder arsip Astro tidak ditemukan: {$source}");
            $this->line('Pindahkan project Astro lama ke /legacy-astro-site/ terlebih dahulu (Architecture-CMS-Admin.md bagian 7.0).');

            return null;
        }

        return rtrim($resolved, DIRECTORY_SEPARATOR);
    }

    /**
     * Ambil semua file Markdown secara rekursif, diurutkan berdasarkan path.
     *
     * @return list<SplFileInfo>
     */
    private function markdownFiles(Filesystem $files, string $directory): array
    {
        return collect($files->allFiles($directory))
            ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'md')
            ->sortBy(fn (SplFileInfo $file): string => $file->getRelativePathname())
            ->values()
            ->all();
    }

    /**
     * Jalankan satu file dalam transaction terpisah agar kegagalan tidak menghentikan file lain.
     */
    private function runFile(string $type, string $relativePath, Closure $callback): void
    {
        try {
            $action = $this->dryRun
                ? $callback(false)
                : DB::transaction(fn (): string => $callback(true));

            $this->summary[$type][$action]++;

            $this->line("  OK     {$relativePath} ({$action})");
        } catch (Throwable $exception) {
            $this->summary[$type]['gagal']++;
            $this->failures[] = [
                'file' => $relativePath,
                'reason' => $exception->getMessage(),
            ];

            Log::warning('Impor konten legacy gagal.', [
                'file' => $relativePath,
                'reason' => $exception->getMessage(),
            ]);

            $this->error("  GAGAL  {$relativePath}: {$exception->getMessage()}");
        }
    }

    /**
     * Impor satu file berita ke tabel posts.
     */
    private function importPost(SplFileInfo $file, bool $write): string
    {
        $parsed = $this->parseMarkdown($file->getPathname());
        $data = $parsed['data'];

        $judul = $this->requireString($data, 'judul', 'Berita');
        $tanggal = $this->parseDate($data, 'tanggal', 'Berita');
        $kategori = $this->requireEnum($data, 'kategori', array_keys(Post::KATEGORI), 'Berita');
        $ringkasan = $this->optionalString($data, 'ringkasan');
        $instagramUrl = $this->optionalString($data, 'instagramUrl');
        $sampul = $this->optionalString($data, 'sampul');
        $sematkan = $this->booleanValue($data, 'sematkan', false);

        if ($instagramUrl !== null && filter_var($instagramUrl, FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException("Berita: field 'instagramUrl' bukan URL yang valid.");
        }

        if ($instagramUrl !== null && preg_match('#^https://(www\.)?instagram\.com/(p|reel)/[A-Za-z0-9_-]+/?#', $instagramUrl) !== 1) {
            throw new RuntimeException("Berita: field 'instagramUrl' harus berupa tautan postingan atau Reel Instagram.");
        }

        $coverPath = $sampul !== null
            ? $this->legacyImagePath($sampul)
            : null;

        $slug = $this->slugFromFile($file);
        $isi = $this->buildPostContent($parsed['body'], $ringkasan, $instagramUrl, $judul);

        $attributes = [
            'type' => 'berita',
            'judul' => $judul,
            'slug' => $slug,
            'ringkasan' => $ringkasan,
            'isi' => $isi,
            'kategori' => $kategori,
            'status' => 'publish',
            'sematkan' => $sematkan,
            'tanggal_publish' => $tanggal->toDateString(),
        ];

        $existing = Post::query()->where('slug', $slug)->first();

        if ($existing !== null && $existing->judul !== $judul) {
            throw new RuntimeException(
                "Slug '{$slug}' sudah dipakai post lain dengan judul berbeda ('{$existing->judul}').",
            );
        }

        if (! $write) {
            return 'valid';
        }

        $post = $existing ?? new Post;
        $post->fill($attributes);
        $post->save();

        if ($coverPath !== null) {
            $this->syncPostCover($post, $coverPath);
        }

        return $existing !== null ? 'diperbarui' : 'dibuat';
    }

    /**
     * Impor satu file galeri beserta foto fisiknya ke Media Library.
     */
    private function importGallery(SplFileInfo $file, bool $write): string
    {
        $parsed = $this->parseMarkdown($file->getPathname());
        $data = $parsed['data'];

        $album = $this->optionalString($data, 'album') ?? 'Kegiatan Madrasah';
        $keterangan = $this->optionalString($data, 'keterangan') ?? '';
        $photos = $this->stringList($data, 'foto', 'Galeri');

        $photoPaths = [];
        $fileNames = [];

        foreach ($photos as $photo) {
            $path = $this->legacyImagePath($photo);
            $fileName = basename($path);

            if (isset($fileNames[$fileName])) {
                throw new RuntimeException("Galeri: nama file foto '{$fileName}' muncul lebih dari sekali dalam satu album.");
            }

            $fileNames[$fileName] = true;
            $photoPaths[] = $path;
        }

        $existing = Gallery::query()->where('album', $album)->first();

        if (! $write) {
            return 'valid';
        }

        $gallery = $existing ?? new Gallery;
        $gallery->fill([
            'album' => $album,
            'keterangan' => $keterangan,
        ]);
        $gallery->save();

        /** @var array<string, GalleryImage> $existingImages */
        $existingImages = [];

        foreach ($gallery->images()->with('media')->get() as $image) {
            $fileName = $image->getFirstMedia('foto')?->file_name;

            if ($fileName !== null) {
                $existingImages[$fileName] = $image;
            }
        }

        foreach ($photoPaths as $index => $photoPath) {
            $fileName = basename($photoPath);
            $image = $existingImages[$fileName] ?? null;

            if ($image !== null) {
                $image->update(['urutan' => $index + 1]);

                continue;
            }

            $image = $gallery->images()->create([
                'urutan' => $index + 1,
            ]);

            $image->addMedia($photoPath)
                ->preservingOriginal()
                ->usingFileName($fileName)
                ->toMediaCollection('foto');

            $existingImages[$fileName] = $image;
            $this->mediaAdded++;
        }

        return $existing !== null ? 'diperbarui' : 'dibuat';
    }

    /**
     * Impor satu file prestasi ke tabel achievements.
     */
    private function importAchievement(SplFileInfo $file, bool $write): string
    {
        $parsed = $this->parseMarkdown($file->getPathname());
        $data = $parsed['data'];

        $judul = $this->requireString($data, 'judul', 'Prestasi');
        $peringkat = $this->requireString($data, 'peringkat', 'Prestasi');
        $tingkat = $this->requireEnum($data, 'tingkat', array_keys(Achievement::TINGKAT), 'Prestasi');
        $bidang = $this->requireEnum($data, 'bidang', array_keys(Achievement::BIDANG), 'Prestasi');
        $tahun = $this->integerValue($data, 'tahun', null, 'Prestasi');
        $penyelenggara = $this->optionalString($data, 'penyelenggara');

        $existing = Achievement::query()
            ->where('judul', $judul)
            ->where('tahun', $tahun)
            ->where('penyelenggara', $penyelenggara)
            ->first();

        if (! $write) {
            return 'valid';
        }

        $achievement = $existing ?? new Achievement;
        $achievement->fill([
            'judul' => $judul,
            'peringkat' => $peringkat,
            'tingkat' => $tingkat,
            'bidang' => $bidang,
            'tahun' => $tahun,
            'penyelenggara' => $penyelenggara,
        ]);
        $achievement->save();

        return $existing !== null ? 'diperbarui' : 'dibuat';
    }

    /**
     * Baca front-matter YAML dan body Markdown dari satu file.
     *
     * @return array{data: array<string, mixed>, body: string}
     */
    private function parseMarkdown(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException('File tidak dapat dibaca.');
        }

        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;

        if (! preg_match('/^---\R(.*?)\R---\R?(.*)$/s', $contents, $matches)) {
            throw new RuntimeException('Front-matter YAML tidak ditemukan atau tidak ditutup dengan ---.');
        }

        $data = $this->parseFrontMatter($matches[1]);

        return [
            'data' => $data,
            'body' => trim($matches[2]),
        ];
    }

    /**
     * Parse front-matter YAML datar yang dipakai koleksi Astro lama.
     *
     * Parser ini sengaja tidak memakai dependency tambahan karena symfony/yaml
     * hanya tersedia sebagai dependency dev. Format yang didukung: pasangan
     * key-value skalar dan list string (field foto).
     *
     * @return array<string, mixed>
     */
    private function parseFrontMatter(string $yaml): array
    {
        $data = [];
        $currentListKey = null;
        $lines = preg_split('/\R/', $yaml) ?: [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            $lineNumber = $index + 1;

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (preg_match('/^([A-Za-z][A-Za-z0-9_]*):\s*(.*)$/', $line, $matches) === 1) {
                $key = $matches[1];
                $rawValue = trim($matches[2]);

                if ($rawValue === '') {
                    $data[$key] = [];
                    $currentListKey = $key;

                    continue;
                }

                $data[$key] = $this->parseScalar($rawValue, $lineNumber);
                $currentListKey = null;

                continue;
            }

            if ($currentListKey !== null && preg_match('/^-\s+(.*)$/', $line, $matches) === 1) {
                $data[$currentListKey][] = $this->parseScalar(trim($matches[1]), $lineNumber);

                continue;
            }

            throw new RuntimeException(
                "Front-matter YAML tidak dapat diparse pada baris {$lineNumber}: {$line}",
            );
        }

        return $data;
    }

    /**
     * Parse satu nilai skalar YAML sederhana.
     */
    private function parseScalar(string $rawValue, int $lineNumber): mixed
    {
        if ($rawValue === '') {
            return null;
        }

        if (str_starts_with($rawValue, '"')) {
            if (strlen($rawValue) < 2 || ! str_ends_with($rawValue, '"')) {
                throw new RuntimeException(
                    "String dengan tanda kutip ganda tidak ditutup pada baris {$lineNumber}.",
                );
            }

            return stripcslashes(substr($rawValue, 1, -1));
        }

        if (str_starts_with($rawValue, "'")) {
            if (strlen($rawValue) < 2 || ! str_ends_with($rawValue, "'")) {
                throw new RuntimeException(
                    "String dengan tanda kutip tunggal tidak ditutup pada baris {$lineNumber}.",
                );
            }

            return str_replace("''", "'", substr($rawValue, 1, -1));
        }

        if (in_array($rawValue, ['|', '>'], true)) {
            throw new RuntimeException(
                "Block scalar YAML tidak didukung pada baris {$lineNumber}. Gunakan string satu baris.",
            );
        }

        $lowerValue = strtolower($rawValue);

        if (in_array($lowerValue, ['true', 'false'], true)) {
            return $lowerValue === 'true';
        }

        if (in_array($lowerValue, ['null', '~'], true)) {
            return null;
        }

        if (preg_match('/^-?\d+$/', $rawValue) === 1) {
            return (int) $rawValue;
        }

        return $rawValue;
    }

    /**
     * Ambil field teks wajib dari front-matter.
     *
     * @param  array<string, mixed>  $data
     */
    private function requireString(array $data, string $key, string $context): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || trim($value) === '') {
            throw new RuntimeException("{$context}: field '{$key}' wajib berupa teks dan tidak boleh kosong.");
        }

        return trim($value);
    }

    /**
     * Ambil field teks opsional dari front-matter.
     *
     * @param  array<string, mixed>  $data
     */
    private function optionalString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new RuntimeException("Field '{$key}' harus berupa teks.");
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Ambil field yang nilainya harus salah satu dari daftar yang diizinkan.
     *
     * @param  array<string, mixed>  $data
     * @param  list<string>  $allowed
     */
    private function requireEnum(array $data, string $key, array $allowed, string $context): string
    {
        $value = $this->requireString($data, $key, $context);

        if (! in_array($value, $allowed, true)) {
            throw new RuntimeException(
                "{$context}: nilai field '{$key}' tidak dikenal ('{$value}'). Pilihan: ".implode(', ', $allowed).'.',
            );
        }

        return $value;
    }

    /**
     * Ambil field angka dari front-matter.
     *
     * @param  array<string, mixed>  $data
     */
    private function integerValue(array $data, string $key, ?int $default = null, string $context = 'Data'): int
    {
        $value = $data[$key] ?? null;

        if ($value === null && $default !== null) {
            return $default;
        }

        if (! is_int($value) && ! (is_string($value) && preg_match('/^-?\d+$/', $value) === 1)) {
            throw new RuntimeException("{$context}: field '{$key}' wajib berupa angka bulat.");
        }

        return (int) $value;
    }

    /**
     * Ambil field boolean dari front-matter.
     *
     * @param  array<string, mixed>  $data
     */
    private function booleanValue(array $data, string $key, bool $default): bool
    {
        $value = $data[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) && in_array($value, [0, 1], true)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($parsed !== null) {
                return $parsed;
            }
        }

        throw new RuntimeException("Field '{$key}' harus berupa boolean.");
    }

    /**
     * Ambil field tanggal dari front-matter.
     *
     * @param  array<string, mixed>  $data
     */
    private function parseDate(array $data, string $key, string $context): CarbonImmutable
    {
        $value = $data[$key] ?? null;

        try {
            if ($value instanceof DateTimeInterface) {
                return CarbonImmutable::createFromInterface($value);
            }

            if (is_int($value)) {
                return CarbonImmutable::createFromTimestamp($value);
            }

            if (is_string($value) && trim($value) !== '') {
                return CarbonImmutable::parse($value);
            }
        } catch (Throwable $exception) {
            throw new RuntimeException("{$context}: field '{$key}' bukan tanggal yang valid.", 0, $exception);
        }

        throw new RuntimeException("{$context}: field '{$key}' wajib berupa tanggal.");
    }

    /**
     * Ambil daftar teks wajib dari front-matter.
     *
     * @param  array<string, mixed>  $data
     * @return list<string>
     */
    private function stringList(array $data, string $key, string $context): array
    {
        $value = $data[$key] ?? null;

        if (! is_array($value) || $value === []) {
            throw new RuntimeException("{$context}: field '{$key}' wajib berupa daftar yang tidak kosong.");
        }

        $result = [];

        foreach ($value as $item) {
            if (! is_string($item) || trim($item) === '') {
                throw new RuntimeException("{$context}: semua item field '{$key}' harus berupa teks dan tidak boleh kosong.");
            }

            $result[] = trim($item);
        }

        return $result;
    }

    /**
     * Buat slug yang stabil dari nama file lama.
     */
    private function slugFromFile(SplFileInfo $file): string
    {
        $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $name = preg_replace('/^\d+[-_]+/', '', $name) ?? $name;
        $slug = Str::slug($name);

        if ($slug === '') {
            throw new RuntimeException('Tidak dapat membuat slug dari nama file.');
        }

        return $slug;
    }

    /**
     * Susun HTML isi post dari body Markdown dan tautan Instagram lama.
     */
    private function buildPostContent(string $body, ?string $ringkasan, ?string $instagramUrl, string $judul): string
    {
        $parts = [];

        if ($body !== '') {
            $paragraphs = preg_split('/\R{2,}/', $body) ?: [];

            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);

                if ($paragraph !== '') {
                    $parts[] = '<p>'.nl2br(e($paragraph), false).'</p>';
                }
            }
        }

        if ($instagramUrl !== null) {
            $parts[] = '<p><a href="'.e($instagramUrl).'" target="_blank" rel="noopener noreferrer">Lihat postingan Instagram</a></p>';
        }

        if ($parts === []) {
            $parts[] = '<p>'.e($ringkasan ?? $judul).'</p>';
        }

        return implode("\n", $parts);
    }

    /**
     * Sinkronkan cover post ke Media Library tanpa membuat duplikat saat dijalankan ulang.
     */
    private function syncPostCover(Post $post, string $coverPath): void
    {
        $fileName = basename($coverPath);
        $current = $post->getFirstMedia('sampul');

        if ($current !== null && $current->file_name === $fileName) {
            return;
        }

        if ($current !== null) {
            $post->clearMediaCollection('sampul');
        }

        $post->addMedia($coverPath)
            ->preservingOriginal()
            ->usingFileName($fileName)
            ->toMediaCollection('sampul');

        $this->mediaAdded++;
    }

    /**
     * Ubah path gambar Astro menjadi path absolut di folder public arsip.
     */
    private function legacyImagePath(string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', trim($relativePath)), '/');
        $publicRoot = realpath($this->sourcePath.'/public');

        if ($publicRoot === false) {
            throw new RuntimeException('Folder public pada arsip Astro tidak ditemukan.');
        }

        $candidate = $publicRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $resolved = realpath($candidate);

        if ($resolved === false || ! is_file($resolved)) {
            throw new RuntimeException("File gambar tidak ditemukan: public/{$relativePath}");
        }

        $mimeType = mime_content_type($resolved);

        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new RuntimeException("File gambar harus JPG, PNG, atau WebP: public/{$relativePath}");
        }

        $publicPrefix = rtrim($publicRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if (! str_starts_with($resolved, $publicPrefix)) {
            throw new RuntimeException("Path gambar keluar dari folder public: {$relativePath}");
        }

        return $resolved;
    }

    /**
     * Tampilkan ringkasan akhir dan daftar file yang gagal.
     */
    private function renderSummary(): void
    {
        $this->newLine();
        $this->info('Ringkasan impor'.($this->dryRun ? ' (dry-run)' : '').':');

        $totalFiles = 0;

        foreach (array_keys(self::COLLECTIONS) as $type) {
            $counts = $this->summary[$type];
            $totalFiles += array_sum($counts);

            $this->line(sprintf(
                '  %-9s dibuat: %d, diperbarui: %d, valid: %d, gagal: %d',
                self::LABELS[$type].':',
                $counts['dibuat'],
                $counts['diperbarui'],
                $counts['valid'],
                $counts['gagal'],
            ));
        }

        $this->line("  Total file: {$totalFiles}, media baru: {$this->mediaAdded}, gagal: ".count($this->failures));

        if ($this->dryRun) {
            $this->warn('Dry-run selesai. Tidak ada data yang ditulis.');
        }

        if ($this->failures !== []) {
            $this->newLine();
            $this->error('File yang gagal diproses:');

            foreach ($this->failures as $failure) {
                $this->line("  - {$failure['file']}: {$failure['reason']}");
            }
        }
    }

    /**
     * Kosongkan state ringkasan sebelum command dijalankan.
     */
    private function resetSummary(): void
    {
        $this->summary = [];
        $this->failures = [];
        $this->mediaAdded = 0;

        foreach (array_keys(self::COLLECTIONS) as $type) {
            $this->summary[$type] = [
                'dibuat' => 0,
                'diperbarui' => 0,
                'valid' => 0,
                'gagal' => 0,
            ];
        }
    }
}
