<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_seeder_membuat_satu_akun_super_admin(): void
    {
        $this->seed(UserSeeder::class);

        $user = User::where('role', 'super_admin')->sole();

        $this->assertTrue($user->isSuperAdmin());
        $this->assertTrue($user->aktif);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->canAccessModule('statistik'));
    }

    public function test_password_cadangan_super_admin_tersimpan_terenkripsi(): void
    {
        $this->seed(UserSeeder::class);

        $user = User::where('role', 'super_admin')->sole();
        $raw = DB::table('users')->where('id', $user->id)->value('password_cadangan');

        $this->assertNotEmpty($user->password_cadangan);
        $this->assertNotSame($user->password_cadangan, $raw);
    }

    public function test_user_seeder_aman_dijalankan_berulang(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(1, User::where('role', 'super_admin')->count());
    }
}
