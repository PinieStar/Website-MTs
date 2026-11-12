<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\AchievementManager;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AchievementManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeAchievement(array $overrides = []): Achievement
    {
        return Achievement::create(array_merge([
            'judul' => 'Juara 1 Lomba Cerdas Cermat',
            'peringkat' => 'Juara 1',
            'tingkat' => 'kabupaten',
            'bidang' => 'akademik',
            'tahun' => 2026,
            'penyelenggara' => 'Dinas Pendidikan',
        ], $overrides));
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_prestasi(): void
    {
        $this->get('/admin/achievements')->assertRedirect('/admin/login');
    }

    public function test_admin_bisa_membuka_halaman_prestasi(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/achievements')
            ->assertOk()
            ->assertSee('Daftar Prestasi')
            ->assertSeeLivewire(AchievementManager::class);
    }

    public function test_admin_bisa_membuat_prestasi(): void
    {
        Livewire::actingAs($this->admin())
            ->test(AchievementManager::class)
            ->call('create')
            ->assertSet('showForm', true)
            ->set('judul', 'Juara 1 Olimpiade Matematika')
            ->set('peringkat', 'Juara 1')
            ->set('tingkat', 'provinsi')
            ->set('bidang', 'akademik')
            ->set('tahun', '2026')
            ->set('penyelenggara', 'Dinas Pendidikan Provinsi')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertDispatched('toast');

        $this->assertDatabaseHas('achievements', [
            'judul' => 'Juara 1 Olimpiade Matematika',
            'peringkat' => 'Juara 1',
            'tingkat' => 'provinsi',
            'bidang' => 'akademik',
            'tahun' => 2026,
            'penyelenggara' => 'Dinas Pendidikan Provinsi',
        ]);
    }

    public function test_admin_bisa_mengubah_prestasi(): void
    {
        $achievement = $this->makeAchievement();

        Livewire::actingAs($this->admin())
            ->test(AchievementManager::class)
            ->call('edit', $achievement->id)
            ->assertSet('judul', $achievement->judul)
            ->set('judul', 'Juara 2 Olimpiade Matematika')
            ->set('peringkat', 'Juara 2')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertDatabaseHas('achievements', [
            'id' => $achievement->id,
            'judul' => 'Juara 2 Olimpiade Matematika',
            'peringkat' => 'Juara 2',
        ]);
    }

    public function test_admin_bisa_menghapus_prestasi(): void
    {
        $achievement = $this->makeAchievement();

        Livewire::actingAs($this->admin())
            ->test(AchievementManager::class)
            ->call('confirmDelete', $achievement->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete')
            ->assertSet('showDeleteModal', false)
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }

    public function test_filter_tingkat_menyaring_daftar_prestasi(): void
    {
        $this->makeAchievement(['judul' => 'Prestasi Tingkat Provinsi', 'tingkat' => 'provinsi']);
        $this->makeAchievement(['judul' => 'Prestasi Tingkat Kabupaten', 'tingkat' => 'kabupaten']);

        Livewire::actingAs($this->admin())
            ->test(AchievementManager::class)
            ->set('filterTingkat', 'provinsi')
            ->assertSee('Prestasi Tingkat Provinsi')
            ->assertDontSee('Prestasi Tingkat Kabupaten');
    }

    public function test_data_terbaru_tampil_paling_atas(): void
    {
        $this->makeAchievement(['judul' => 'Prestasi Lama']);
        $this->makeAchievement(['judul' => 'Prestasi Baru']);

        $this->actingAs($this->admin())
            ->get('/admin/achievements')
            ->assertOk()
            ->assertSeeInOrder(['Prestasi Baru', 'Prestasi Lama']);
    }

    public function test_judul_wajib_diisi_saat_menyimpan(): void
    {
        Livewire::actingAs($this->admin())
            ->test(AchievementManager::class)
            ->call('create')
            ->set('judul', '')
            ->call('save')
            ->assertHasErrors(['judul' => 'required']);

        $this->assertDatabaseCount('achievements', 0);
    }
}
