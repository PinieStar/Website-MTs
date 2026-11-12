<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_diarahkan_ke_login_saat_membuka_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_super_admin_bisa_membuka_manajemen_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get('/admin/manajemen-admin')
            ->assertOk()
            ->assertSee('Daftar Akun Admin');
    }

    public function test_admin_biasa_tidak_bisa_membuka_manajemen_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/manajemen-admin')
            ->assertForbidden();
    }

    public function test_super_admin_melihat_menu_manajemen_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Manajemen Admin');
    }

    public function test_admin_biasa_tidak_melihat_menu_manajemen_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertDontSee('Manajemen Admin');
    }

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $user = User::factory()->create(['aktif' => false]);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
