<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'password_cadangan', 'role', 'permissions', 'aktif'])]
#[Hidden(['password', 'password_cadangan', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Modul panel admin yang akses-nya diatur Super Admin lewat centang.
     * Kuncinya disimpan di kolom `permissions` (JSON).
     *
     * @var array<string, string>
     */
    public const MODULES = [
        'berita' => 'Berita',
        'artikel' => 'Artikel',
        'galeri' => 'Galeri',
        'prestasi' => 'Prestasi',
        'statistik' => 'Statistik Madrasah',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_cadangan' => 'encrypted',
            'aktif' => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * Apakah pengguna ini Super Admin?
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Apakah pengguna ini boleh membuka modul tertentu?
     * Super Admin selalu boleh; Admin hanya modul yang dicentang.
     */
    public function canAccessModule(string $module): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($module, $this->permissions ?? [], true);
    }

    /**
     * Post/berita/artikel yang ditulis pengguna ini.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'penulis_id');
    }
}
