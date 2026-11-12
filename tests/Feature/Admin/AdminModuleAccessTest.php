<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AdminUserManager;
use App\Livewire\Admin\GalleryManager;
use App\Livewire\Admin\PostManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<int, string>  $permissions
     */
    private function adminWith(array $permissions): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'permissions' => $permissions,
        ]);
    }

    public function test_admin_hanya_bisa_membuka_halaman_yang_dicentang(): void
    {
        $admin = $this->adminWith(['berita']);

        $this->actingAs($admin)->get('/admin/posts/berita')->assertOk();
        $this->actingAs($admin)->get('/admin/posts/artikel')->assertForbidden();
        $this->actingAs($admin)->get('/admin/galleries')->assertForbidden();
        $this->actingAs($admin)->get('/admin/achievements')->assertForbidden();
        $this->actingAs($admin)->get('/admin/statistics')->assertForbidden();
    }

    public function test_menu_sidebar_hanya_menampilkan_modul_yang_boleh_diakses(): void
    {
        $admin = $this->adminWith(['galeri']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee(route('admin.galleries.index'), false)
            ->assertDontSee(route('admin.posts.berita'), false)
            ->assertDontSee(route('admin.achievements.index'), false)
            ->assertDontSee(route('admin.statistics.index'), false);
    }

    public function test_dashboard_tetap_bisa_dibuka_admin_tanpa_akses_apa_pun(): void
    {
        $admin = $this->adminWith([]);

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/posts/berita')->assertForbidden();
    }

    public function test_super_admin_bisa_membuka_semua_modul(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'permissions' => null]);

        foreach (['/admin/posts/berita', '/admin/posts/artikel', '/admin/galleries', '/admin/achievements', '/admin/statistics'] as $url) {
            $this->actingAs($superAdmin)->get($url)->assertOk();
        }
    }

    public function test_komponen_livewire_juga_menolak_admin_tanpa_akses(): void
    {
        Livewire::actingAs($this->adminWith(['berita']))
            ->test(GalleryManager::class)
            ->assertForbidden();

        Livewire::actingAs($this->adminWith(['galeri']))
            ->test(PostManager::class, ['type' => 'artikel'])
            ->assertForbidden();

        Livewire::actingAs($this->adminWith(['artikel']))
            ->test(PostManager::class, ['type' => 'artikel'])
            ->assertOk();
    }

    public function test_upload_gambar_inline_butuh_akses_berita_atau_artikel(): void
    {
        Storage::fake('public');

        $payload = ['image' => UploadedFile::fake()->image('inline.jpg', 800, 600)->size(512)];

        $this->actingAs($this->adminWith(['galeri']))
            ->postJson(route('admin.posts.inline-image'), $payload)
            ->assertForbidden();

        $this->actingAs($this->adminWith(['artikel']))
            ->postJson(route('admin.posts.inline-image'), [
                'image' => UploadedFile::fake()->image('inline.jpg', 800, 600)->size(512),
            ])
            ->assertOk();
    }

    public function test_super_admin_wajib_mencentang_minimal_satu_akses_saat_membuat_akun(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openCreateModal')
            ->set('name', 'Admin Tanpa Akses')
            ->set('email', 'tanpa.akses@example.com')
            ->set('password', 'password-baru')
            ->set('password_confirmation', 'password-baru')
            ->set('permissions', [])
            ->call('createAdmin')
            ->assertHasErrors(['permissions']);

        $this->assertDatabaseMissing('users', ['email' => 'tanpa.akses@example.com']);
    }

    public function test_akses_tidak_dikenal_ditolak_saat_membuat_akun(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openCreateModal')
            ->set('name', 'Admin Aneh')
            ->set('email', 'aneh@example.com')
            ->set('password', 'password-baru')
            ->set('password_confirmation', 'password-baru')
            ->set('permissions', ['berita', 'manajemen-admin'])
            ->call('createAdmin')
            ->assertHasErrors(['permissions.1']);

        $this->assertDatabaseMissing('users', ['email' => 'aneh@example.com']);
    }

    public function test_super_admin_bisa_mengubah_akses_admin_yang_sudah_ada(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $admin = $this->adminWith(['berita', 'artikel', 'galeri', 'prestasi', 'statistik']);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openAccessModal', $admin->id)
            ->assertSet('showAccessModal', true)
            ->assertSet('accessPermissions', ['berita', 'artikel', 'galeri', 'prestasi', 'statistik'])
            ->set('accessPermissions', ['prestasi', 'berita'])
            ->call('saveAccess')
            ->assertHasNoErrors()
            ->assertSet('showAccessModal', false)
            ->assertDispatched('toast', type: 'success');

        $this->assertSame(['berita', 'prestasi'], $admin->fresh()->permissions);

        $this->actingAs($admin->fresh())->get('/admin/achievements')->assertOk();
        $this->actingAs($admin->fresh())->get('/admin/galleries')->assertForbidden();
    }

    public function test_akses_tidak_bisa_dikosongkan_dan_super_admin_tidak_bisa_diatur(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $admin = $this->adminWith(['berita']);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openAccessModal', $admin->id)
            ->set('accessPermissions', [])
            ->call('saveAccess')
            ->assertHasErrors(['accessPermissions']);

        $this->assertSame(['berita'], $admin->fresh()->permissions);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openAccessModal', $superAdmin->id)
            ->assertSet('showAccessModal', false)
            ->assertDispatched('toast', type: 'danger');
    }

    public function test_admin_biasa_tidak_bisa_mengubah_akses_lewat_livewire(): void
    {
        $admin = $this->adminWith(['berita']);
        $target = $this->adminWith(['berita']);

        Livewire::actingAs($admin)
            ->test(AdminUserManager::class)
            ->assertForbidden();

        $this->assertSame(['berita'], $target->fresh()->permissions);
    }
}
