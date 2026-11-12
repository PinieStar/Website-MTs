<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'album',
        'keterangan',
    ];

    /**
     * Foto-foto di dalam album, diurutkan sesuai kolom urutan.
     */
    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)
            ->orderBy('urutan')
            ->orderBy('id');
    }
}
