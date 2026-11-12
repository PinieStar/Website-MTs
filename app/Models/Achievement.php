<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    /**
     * Pilihan tingkat perlombaan.
     *
     * @var array<string, string>
     */
    public const TINGKAT = [
        'madrasah' => 'Madrasah',
        'kecamatan' => 'Kecamatan',
        'kabupaten' => 'Kabupaten',
        'provinsi' => 'Provinsi',
        'nasional' => 'Nasional',
    ];

    /**
     * Pilihan bidang perlombaan.
     *
     * @var array<string, string>
     */
    public const BIDANG = [
        'akademik' => 'Akademik',
        'keagamaan' => 'Keagamaan',
        'olahraga' => 'Olahraga',
        'seni' => 'Seni',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'judul',
        'peringkat',
        'tingkat',
        'bidang',
        'tahun',
        'penyelenggara',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
        ];
    }

    /**
     * Label tingkat yang ramah dibaca.
     */
    public function labelTingkat(): string
    {
        return self::TINGKAT[$this->tingkat] ?? $this->tingkat;
    }

    /**
     * Label bidang yang ramah dibaca.
     */
    public function labelBidang(): string
    {
        return self::BIDANG[$this->bidang] ?? $this->bidang;
    }
}
