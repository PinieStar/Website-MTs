<?php

namespace App\Models;

use Database\Factories\StatisticFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Statistic extends Model
{
    /** @use HasFactory<StatisticFactory> */
    use HasFactory;

    /**
     * Pilihan ikon untuk kartu statistik.
     *
     * @var array<string, string>
     */
    public const ICONS = [
        'users' => 'Siswa / Orang',
        'graduation-cap' => 'Guru / Pendidikan',
        'heart-handshake' => 'Pendampingan',
        'school' => 'Kelas / Madrasah',
        'book-open' => 'Pembelajaran',
        'trophy' => 'Prestasi',
        'star' => 'Keunggulan',
        'landmark' => 'Lembaga',
        'sparkles' => 'Program',
        'shield' => 'Karakter',
        'globe' => 'Jangkauan',
        'calendar-days' => 'Tahun / Jadwal',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'label',
        'value',
        'icon',
        'urutan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'urutan' => 'integer',
        ];
    }

    /**
     * Urutkan kartu statistik sesuai urutan tampil.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('urutan')->orderBy('id');
    }

    /**
     * Daftar tetap jenis statistik yang boleh ada di "Statistik Madrasah".
     * Admin hanya boleh mengubah angkanya, tidak boleh menambah atau
     * mengurangi jenis statistik ini dari panel admin.
     *
     * @var array<int, array{key: string, label: string, icon: string}>
     */
    public const DEFINITIONS = [
        ['key' => 'students', 'label' => 'Siswa', 'icon' => 'users'],
        ['key' => 'teachers', 'label' => 'Guru', 'icon' => 'graduation-cap'],
        ['key' => 'staff', 'label' => 'Tenaga Kependidikan', 'icon' => 'heart-handshake'],
        ['key' => 'classes', 'label' => 'Kelas', 'icon' => 'school'],
    ];

    /**
     * Nilai bawaan dari konfigurasi situs saat tabel masih kosong.
     *
     * @return Collection<int, self>
     */
    public static function defaults(): Collection
    {
        $stats = config('site.stats', []);

        return collect(self::DEFINITIONS)
            ->values()
            ->map(fn (array $item, int $index): self => new self([
                'label' => $item['label'],
                'value' => (int) ($stats[$item['key']] ?? 0),
                'icon' => $item['icon'],
                'urutan' => $index + 1,
            ]));
    }

    /**
     * Pastikan keempat jenis statistik tetap ada di database, tanpa
     * menimpa angka yang sudah diisi admin. Dipanggil setiap panel
     * admin dibuka supaya datanya selalu lengkap: Siswa, Guru,
     * Tenaga Kependidikan, dan Kelas — tidak lebih, tidak kurang.
     */
    public static function syncDefaults(): void
    {
        $stats = config('site.stats', []);

        foreach (self::DEFINITIONS as $index => $item) {
            self::query()->firstOrCreate(
                ['label' => $item['label']],
                [
                    'value' => (int) ($stats[$item['key']] ?? 0),
                    'icon' => $item['icon'],
                    'urutan' => $index + 1,
                ],
            );
        }
    }
}
