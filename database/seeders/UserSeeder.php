<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Kredensial akun Super Admin awal.
     *
     * Diganti di sini jika perlu. Setelah login pertama, segera ubah
     * password lewat menu Profil / Manajemen Admin.
     */
    private const NAME = 'Super Admin';

    private const EMAIL = 'superadmin@mts.sch.id';

    private const PASSWORD = 'admin123';

    /**
     * Membuat satu akun Super Admin awal.
     *
     * Jalankan `php artisan db:seed --class=UserSeeder`.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => self::NAME,
                'password' => self::PASSWORD,
                'password_cadangan' => self::PASSWORD,
                'role' => 'super_admin',
                'aktif' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
