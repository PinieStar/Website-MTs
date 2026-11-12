<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AdminUserManager;
use App\Models\Achievement;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserManagerTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'super_admin',
            'aktif' => true,
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'aktif' => true,
        ]);
    }

    private function makePost(User $author): Post
    {
        return Post::create([
            'type' => 'berita',
            'judul' => 'Konten milik '.$author->name,
            'ringkasan' => 'Ringkasan konten admin.',
            'isi' => '<p>Isi konten admin yang harus tetap tersimpan.</p>',
            'kategori' => 'berita',
            'status' => 'publish',
            'sematkan' => false,
            'tanggal_publish' => today()->toDateString(),
            'penulis_id' => $author->id,
        ]);
    }

    public function test_admin_biasa_mendapat_403_saat_membuka_manajemen_admin(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/manajemen-admin')
            ->assertForbidden();
    }

    public function test_admin_biasa_tidak_bisa_menjalankan_komponen_manajemen_admin(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(AdminUserManager::class)
            ->assertForbidden();
    }

    public function test_super_admin_melihat_daftar_akun_admin(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();

        $this->actingAs($superAdmin)
            ->get('/admin/manajemen-admin')
            ->assertOk()
            ->assertSee('Daftar Akun Admin')
            ->assertSee($superAdmin->email)
            ->assertSee($admin->email)
            ->assertSeeLivewire(AdminUserManager::class);
    }

    public function test_super_admin_dapat_membuka_kartu_identitas_admin(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();
        $admin->forceFill([
            'permissions' => ['berita', 'galeri'],
            'password_cadangan' => 'password-admin',
        ])->save();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openIdentityModal', $admin->id)
            ->assertSet('showIdentityModal', true)
            ->assertSet('identityId', $admin->id)
            ->assertSet('identityName', $admin->name)
            ->assertSet('identityEmail', $admin->email)
            ->assertSet('identityRole', 'admin')
            ->assertSet('identityAktif', true)
            ->assertSet('identityPermissions', ['berita', 'galeri'])
            ->assertSet('identityHasPassword', true)
            ->assertSet('identityPasswordVisible', true)
            ->assertSet('identityPassword', 'password-admin')
            ->assertSee('Kartu Identitas Admin')
            ->assertSee('password-admin')
            ->assertSee('Sembunyikan')
            ->assertSee('Berita')
            ->assertSee('Galeri')
            ->assertDontSee($admin->password)
            ->call('closeIdentityModal')
            ->assertSet('showIdentityModal', false)
            ->assertSet('identityId', null)
            ->assertSet('identityPassword', '')
            ->assertSet('identityPasswordVisible', false);
    }

    public function test_super_admin_dapat_menyembunyikan_dan_menampilkan_password_admin(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();
        $admin->forceFill(['password_cadangan' => 'password-admin'])->save();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openIdentityModal', $admin->id)
            ->assertSet('identityPasswordVisible', true)
            ->assertSee('password-admin')
            ->call('hideIdentityPassword')
            ->assertSet('identityPasswordVisible', false)
            ->assertSet('identityPassword', '')
            ->assertSee('••••••••')
            ->assertSee('Tampilkan')
            ->assertDontSee('password-admin')
            ->call('showIdentityPassword')
            ->assertSet('identityPasswordVisible', true)
            ->assertSet('identityPassword', 'password-admin')
            ->assertSee('password-admin');
    }

    public function test_password_cadangan_disimpan_terenkripsi_saat_admin_dibuat(): void
    {
        $superAdmin = $this->superAdmin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openCreateModal')
            ->set('name', 'Admin Rahasia')
            ->set('email', 'rahasia@example.com')
            ->set('password', 'password-rahasia')
            ->set('password_confirmation', 'password-rahasia')
            ->set('permissions', ['berita'])
            ->call('createAdmin')
            ->assertHasNoErrors();

        $created = User::where('email', 'rahasia@example.com')->firstOrFail();
        $raw = DB::table('users')->where('id', $created->id)->value('password_cadangan');

        $this->assertNotSame('password-rahasia', $raw);
        $this->assertSame('password-rahasia', $created->password_cadangan);
        $this->assertTrue(Hash::check('password-rahasia', $created->password));
    }

    public function test_reset_password_memperbarui_password_cadangan(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();
        $admin->forceFill(['password_cadangan' => 'password-lama'])->save();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openResetModal', $admin->id)
            ->set('newPassword', 'password-baru')
            ->set('newPassword_confirmation', 'password-baru')
            ->call('resetPassword')
            ->assertHasNoErrors();

        $admin->refresh();

        $this->assertSame('password-baru', $admin->password_cadangan);
        $this->assertTrue(Hash::check('password-baru', $admin->password));
    }

    public function test_password_yang_diubah_lewat_profil_ikut_tersimpan_untuk_super_admin(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('password.update'), [
                'current_password' => 'password',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertSessionHasNoErrors();

        $admin->refresh();

        $this->assertSame('password-baru', $admin->password_cadangan);
        $this->assertTrue(Hash::check('password-baru', $admin->password));
    }

    public function test_kartu_identitas_super_admin_menampilkan_semua_menu(): void
    {
        $superAdmin = $this->superAdmin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openIdentityModal', $superAdmin->id)
            ->assertSet('identityRole', 'super_admin')
            ->assertSee('Semua menu')
            ->assertSee('Akun Anda');
    }

    public function test_kartu_identitas_bisa_langsung_membuka_reset_password_dan_atur_akses(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openIdentityModal', $admin->id)
            ->call('resetFromIdentity')
            ->assertSet('showIdentityModal', false)
            ->assertSet('showResetModal', true)
            ->assertSet('resettingId', $admin->id)
            ->call('closeResetModal')
            ->call('openIdentityModal', $admin->id)
            ->call('editAccessFromIdentity')
            ->assertSet('showIdentityModal', false)
            ->assertSet('showAccessModal', true)
            ->assertSet('accessId', $admin->id);
    }

    public function test_super_admin_dapat_menambah_admin_baru_dengan_role_admin(): void
    {
        $superAdmin = $this->superAdmin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->set('name', 'Admin Baru')
            ->set('email', 'ADMIN.BARU@example.com')
            ->set('password', 'password-baru')
            ->set('password_confirmation', 'password-baru')
            ->set('permissions', ['berita', 'galeri'])
            ->call('createAdmin')
            ->assertHasNoErrors()
            ->assertSet('showCreateModal', false)
            ->assertSee('admin.baru@example.com')
            ->assertDispatched('toast', type: 'success');

        $created = User::where('email', 'admin.baru@example.com')->firstOrFail();

        $this->assertSame('admin', $created->role);
        $this->assertSame(['berita', 'galeri'], $created->permissions);
        $this->assertTrue($created->aktif);
        $this->assertNotNull($created->email_verified_at);
        $this->assertTrue(Hash::check('password-baru', $created->password));
    }

    public function test_tambah_admin_menolak_email_yang_sudah_dipakai(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openCreateModal')
            ->set('name', 'Admin Duplikat')
            ->set('email', $admin->email)
            ->set('password', 'password-baru')
            ->set('password_confirmation', 'password-baru')
            ->call('createAdmin')
            ->assertHasErrors(['email' => 'unique'])
            ->assertSee('Email sudah digunakan akun lain.');

        $this->assertSame(2, User::count());
    }

    public function test_tambah_admin_menolak_konfirmasi_password_yang_tidak_cocok(): void
    {
        $superAdmin = $this->superAdmin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openCreateModal')
            ->set('name', 'Admin Baru')
            ->set('email', 'admin.baru@example.com')
            ->set('password', 'password-baru')
            ->set('password_confirmation', 'password-berbeda')
            ->call('createAdmin')
            ->assertHasErrors(['password' => 'confirmed'])
            ->assertSee('Konfirmasi password tidak cocok.');

        $this->assertSame(1, User::count());
    }

    public function test_super_admin_bisa_menonaktifkan_dan_mengaktifkan_admin(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();

        DB::table('sessions')->insert([
            'id' => 'admin-session-toggle',
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('toggleAktif', $admin->id)
            ->assertDispatched('toast', type: 'success');

        $this->assertFalse($admin->fresh()->aktif);
        $this->assertDatabaseMissing('sessions', ['id' => 'admin-session-toggle']);

        auth()->logout();

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('toggleAktif', $admin->id)
            ->assertDispatched('toast', type: 'success');

        $this->assertTrue($admin->fresh()->aktif);
    }

    public function test_super_admin_tidak_bisa_menonaktifkan_akun_sendiri(): void
    {
        $superAdmin = $this->superAdmin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('toggleAktif', $superAdmin->id)
            ->assertDispatched('toast', type: 'danger');

        $this->assertTrue($superAdmin->fresh()->aktif);
    }

    public function test_super_admin_tidak_bisa_menghapus_akun_sendiri(): void
    {
        $superAdmin = $this->superAdmin();

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('confirmDelete', $superAdmin->id)
            ->assertSet('showDeleteModal', false)
            ->assertDispatched('toast', type: 'danger');

        $this->assertModelExists($superAdmin);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->set('deletingId', $superAdmin->id)
            ->call('deleteUser')
            ->assertDispatched('toast', type: 'danger');

        $this->assertModelExists($superAdmin);
    }

    public function test_super_admin_bisa_reset_password_admin_lain(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();
        $oldRememberToken = $admin->remember_token;

        DB::table('sessions')->insert([
            'id' => 'admin-session',
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openResetModal', $admin->id)
            ->assertSet('showResetModal', true)
            ->set('newPassword', 'password-baru')
            ->set('newPassword_confirmation', 'password-baru')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertSet('showResetModal', false)
            ->assertDispatched('toast', type: 'success');

        $admin->refresh();

        $this->assertTrue(Hash::check('password-baru', $admin->password));
        $this->assertFalse(Hash::check('password', $admin->password));
        $this->assertNotSame($oldRememberToken, $admin->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'admin-session']);
    }

    public function test_super_admin_tidak_bisa_reset_password_akun_sendiri(): void
    {
        $superAdmin = $this->superAdmin();
        $oldPassword = $superAdmin->password;

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('openResetModal', $superAdmin->id)
            ->assertSet('showResetModal', false)
            ->assertDispatched('toast', type: 'danger');

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->set('resettingId', $superAdmin->id)
            ->set('newPassword', 'password-baru')
            ->set('newPassword_confirmation', 'password-baru')
            ->call('resetPassword')
            ->assertDispatched('toast', type: 'danger');

        $this->assertSame($oldPassword, $superAdmin->fresh()->password);
    }

    public function test_hapus_admin_tidak_menghapus_konten_yang_pernah_dibuat(): void
    {
        $superAdmin = $this->superAdmin();
        $admin = $this->admin();
        $post = $this->makePost($admin);

        $gallery = Gallery::create([
            'album' => 'Kegiatan Madrasah',
            'keterangan' => 'Dokumentasi kegiatan madrasah.',
        ]);

        $achievement = Achievement::create([
            'judul' => 'Juara Olimpiade',
            'peringkat' => 'Juara 1',
            'tingkat' => 'kabupaten',
            'bidang' => 'akademik',
            'tahun' => 2026,
            'penyelenggara' => 'Dinas Pendidikan',
        ]);

        Livewire::actingAs($superAdmin)
            ->test(AdminUserManager::class)
            ->call('confirmDelete', $admin->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deletingPostCount', 1)
            ->call('deleteUser')
            ->assertSet('showDeleteModal', false)
            ->assertDispatched('toast', type: 'success');

        $this->assertModelMissing($admin);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'penulis_id' => null,
        ]);
        $this->assertSame($post->judul, $post->fresh()->judul);
        $this->assertModelExists($gallery);
        $this->assertModelExists($achievement);
    }
}
