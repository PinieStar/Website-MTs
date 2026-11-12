<?php

namespace App\Models;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    /**
     * Pilihan tipe post.
     *
     * @var array<string, string>
     */
    public const TYPE = [
        'berita' => 'Berita',
        'artikel' => 'Artikel',
    ];

    /**
     * Pilihan kategori khusus berita.
     *
     * @var array<string, string>
     */
    public const KATEGORI = [
        'berita' => 'Berita',
        'pengumuman' => 'Pengumuman',
        'kegiatan' => 'Kegiatan',
    ];

    /**
     * Pilihan status tayang.
     *
     * @var array<string, string>
     */
    public const STATUS = [
        'draft' => 'Draft',
        'publish' => 'Publish',
    ];

    /**
     * Tag HTML yang diizinkan di isi artikel.
     *
     * @var list<string>
     */
    private const ALLOWED_HTML_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'mark', 'span',
        'h1', 'h2', 'h3', 'h4', 'ul', 'ol', 'li',
        'a', 'img', 'blockquote', 'hr', 'code', 'pre',
    ];

    /**
     * Atribut HTML yang diizinkan per tag.
     *
     * @var array<string, list<string>>
     */
    private const ALLOWED_HTML_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel', 'title'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'p' => ['style'],
        'h1' => ['style'],
        'h2' => ['style'],
        'h3' => ['style'],
        'h4' => ['style'],
        'span' => ['style'],
        'mark' => ['style'],
    ];

    /**
     * Properti CSS yang diizinkan pada atribut style.
     *
     * @var list<string>
     */
    private const ALLOWED_CSS_PROPERTIES = [
        'text-align', 'color', 'background-color', 'font-size',
    ];

    /**
     * Tag berbahaya yang dibuang beserta isinya.
     *
     * @var list<string>
     */
    private const STRIPPED_HTML_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'form', 'input',
        'button', 'textarea', 'select', 'option', 'svg', 'math', 'link',
        'meta', 'base',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'kategori',
        'status',
        'sematkan',
        'tanggal_publish',
        'penulis_id',
        'penulis_nama',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sematkan' => 'boolean',
            'tanggal_publish' => 'date',
        ];
    }

    /**
     * Pengaturan pembuatan slug otomatis dari judul.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('judul')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Media library: satu gambar sampul per post.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('sampul')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Penulis post.
     */
    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    /**
     * Nama penulis yang ditampilkan di halaman publik.
     *
     * Prioritas: nama penulis manual, lalu akun admin pembuat post,
     * lalu nama redaksi sebagai cadangan.
     */
    public function namaPenulis(): string
    {
        if (filled($this->penulis_nama)) {
            return $this->penulis_nama;
        }

        return $this->penulis?->name ?? 'Redaksi MTs NU Al Syairiyah';
    }

    /**
     * Hanya post yang sudah tayang dan tanggalnya sudah lewat.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'publish')
            ->whereDate('tanggal_publish', '<=', today());
    }

    /**
     * Hanya post dengan tipe tertentu (berita/artikel).
     */
    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Label tipe yang ramah dibaca.
     */
    public function labelType(): string
    {
        return self::TYPE[$this->type] ?? $this->type;
    }

    /**
     * Label kategori yang ramah dibaca.
     */
    public function labelKategori(): string
    {
        return self::KATEGORI[$this->kategori] ?? '-';
    }

    /**
     * Label status yang ramah dibaca.
     */
    public function labelStatus(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /**
     * Tanggal publish dalam format Indonesia.
     */
    public function tanggalPublishLabel(): string
    {
        return $this->tanggal_publish?->locale('id')->translatedFormat('d F Y') ?? '-';
    }

    /**
     * Isi artikel yang sudah dibersihkan untuk dirender sebagai HTML.
     *
     * Saat $themeAware aktif, warna teks dari editor diubah menjadi warna
     * yang ikut menyesuaikan tema terang/gelap halaman publik.
     */
    public function sanitizedIsi(bool $themeAware = false): string
    {
        return self::sanitizeHtml($this->isi, $themeAware);
    }

    /**
     * Bersihkan HTML dari tag, atribut, dan URL yang tidak diizinkan.
     */
    public static function sanitizeHtml(string $html, bool $themeAware = false): string
    {
        if (trim($html) === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);

        $document = new DOMDocument;
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return '';
        }

        $wrapper = $document->getElementsByTagName('div')->item(0);

        if (! $wrapper) {
            return '';
        }

        foreach (iterator_to_array($wrapper->childNodes) as $child) {
            self::sanitizeHtmlNode($child, $themeAware);
        }

        $output = '';

        foreach ($wrapper->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Bersihkan satu node HTML secara rekursif.
     */
    private static function sanitizeHtmlNode(DOMNode $node, bool $themeAware): void
    {
        if (! $node instanceof DOMElement) {
            return;
        }

        $tag = strtolower($node->nodeName);

        if (in_array($tag, self::STRIPPED_HTML_TAGS, true)) {
            $node->parentNode?->removeChild($node);

            return;
        }

        if (! in_array($tag, self::ALLOWED_HTML_TAGS, true)) {
            foreach (iterator_to_array($node->childNodes) as $child) {
                self::sanitizeHtmlNode($child, $themeAware);
            }

            $parent = $node->parentNode;

            if ($parent) {
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }

                $parent->removeChild($node);
            }

            return;
        }

        $allowedAttributes = self::ALLOWED_HTML_ATTRIBUTES[$tag] ?? [];

        foreach (iterator_to_array($node->attributes) as $attribute) {
            $name = strtolower($attribute->nodeName);

            if (! in_array($name, $allowedAttributes, true)) {
                $node->removeAttribute($attribute->nodeName);

                continue;
            }

            if ($name === 'style') {
                $style = self::sanitizeStyle((string) $attribute->nodeValue, $themeAware);

                if ($style === '') {
                    $node->removeAttribute($attribute->nodeName);
                } else {
                    $node->setAttribute('style', $style);
                }

                continue;
            }

            if (in_array($name, ['href', 'src'], true)
                && ! self::isSafeHtmlUrl((string) $attribute->nodeValue, $tag)) {
                $node->removeAttribute($attribute->nodeName);
            }
        }

        if ($tag === 'img' && ! $node->getAttribute('src')) {
            $node->parentNode?->removeChild($node);

            return;
        }

        if ($tag === 'a' && $node->getAttribute('target') === '_blank') {
            $node->setAttribute('rel', 'noopener noreferrer nofollow');
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            self::sanitizeHtmlNode($child, $themeAware);
        }
    }

    /**
     * Saring atribut style agar hanya menyisakan properti yang aman.
     */
    private static function sanitizeStyle(string $style, bool $themeAware): string
    {
        $allowed = [];

        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);

            if (! in_array($property, self::ALLOWED_CSS_PROPERTIES, true)) {
                continue;
            }

            if (! self::isSafeCssValue($property, $value)) {
                continue;
            }

            if ($themeAware && $property === 'color') {
                $value = self::themeAwareColor($value);

                // Warna netral dibuang agar teks mengikuti warna tema.
                if ($value === null) {
                    continue;
                }
            }

            $allowed[] = $property.': '.$value;
        }

        return implode('; ', $allowed);
    }

    /**
     * Pastikan nilai CSS yang dipakai editor tidak membawa muatan berbahaya.
     */
    private static function isSafeCssValue(string $property, string $value): bool
    {
        return match ($property) {
            'text-align' => in_array(strtolower($value), ['left', 'right', 'center', 'justify'], true),
            'color', 'background-color' => preg_match('/^#[0-9a-f]{3,8}$/i', $value) === 1
                || preg_match('/^rgba?\(\s*[\d.,%\s\/]+\)$/i', $value) === 1,
            'font-size' => preg_match('/^(xx-small|x-small|small|medium|large|x-large|xx-large|[\d.]+(px|em|rem|%))$/i', $value) === 1,
            default => false,
        };
    }

    /**
     * Ubah warna teks pilihan editor agar tetap terbaca di tema terang
     * maupun gelap. Warna netral dibuang agar mengikuti warna teks tema;
     * warna lain dibuat versi terang dan gelapnya lewat light-dark().
     */
    private static function themeAwareColor(string $value): ?string
    {
        $rgb = self::parseCssColor($value);

        if ($rgb === null) {
            return $value;
        }

        [$red, $green, $blue] = $rgb;

        // Hitam, putih, dan abu-abu netral cukup mengikuti warna teks tema.
        if (max($red, $green, $blue) - min($red, $green, $blue) < 12) {
            return null;
        }

        [$hue, $saturation, $lightness] = self::rgbToHsl($red, $green, $blue);

        $lightVariant = sprintf('hsl(%d, %d%%, %d%%)', $hue, $saturation, min($lightness, 34));
        $darkVariant = sprintf('hsl(%d, %d%%, %d%%)', $hue, $saturation, max($lightness, 74));

        return "light-dark({$lightVariant}, {$darkVariant})";
    }

    /**
     * Ambil komponen RGB dari warna CSS yang sudah lolos validasi.
     *
     * @return array{int, int, int}|null
     */
    private static function parseCssColor(string $value): ?array
    {
        $value = trim($value);

        if (preg_match('/^#([0-9a-f]{3,8})$/i', $value, $matches) === 1) {
            $hex = $matches[1];

            if (in_array(strlen($hex), [3, 4], true)) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }

            if (strlen($hex) < 6) {
                return null;
            }

            return [
                (int) hexdec(substr($hex, 0, 2)),
                (int) hexdec(substr($hex, 2, 2)),
                (int) hexdec(substr($hex, 4, 2)),
            ];
        }

        if (preg_match('/^rgba?\(\s*([\d.]+)\s*[,\s]\s*([\d.]+)\s*[,\s]\s*([\d.]+)/i', $value, $matches) === 1) {
            return [
                (int) min(255, (float) $matches[1]),
                (int) min(255, (float) $matches[2]),
                (int) min(255, (float) $matches[3]),
            ];
        }

        return null;
    }

    /**
     * Konversi RGB ke HSL (hue derajat, saturation & lightness persen).
     *
     * @return array{int, int, int}
     */
    private static function rgbToHsl(int $red, int $green, int $blue): array
    {
        $red /= 255;
        $green /= 255;
        $blue /= 255;

        $max = max($red, $green, $blue);
        $min = min($red, $green, $blue);
        $lightness = ($max + $min) / 2;

        if ($max === $min) {
            return [0, 0, (int) round($lightness * 100)];
        }

        $delta = $max - $min;
        $saturation = $lightness > 0.5
            ? $delta / (2 - $max - $min)
            : $delta / ($max + $min);

        $hue = match ($max) {
            $red => (($green - $blue) / $delta) + ($green < $blue ? 6 : 0),
            $green => (($blue - $red) / $delta) + 2,
            default => (($red - $green) / $delta) + 4,
        };

        return [
            (int) round($hue * 60),
            (int) round($saturation * 100),
            (int) round($lightness * 100),
        ];
    }

    /**
     * Pastikan URL hanya memakai skema yang aman.
     */
    private static function isSafeHtmlUrl(string $url, string $tag): bool
    {
        $url = trim($url);

        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return true;
        }

        if (preg_match('/^https?:\/\//i', $url) === 1) {
            return true;
        }

        return $tag === 'a' && preg_match('/^(mailto:|tel:)/i', $url) === 1;
    }
}
