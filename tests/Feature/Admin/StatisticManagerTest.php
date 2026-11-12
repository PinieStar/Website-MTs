<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\StatisticManager;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatisticManagerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_statistik(): void
    {
        $this->get('/admin/statistics')->assertRedirect('/admin/login');
    }

    public function test_admin_bisa_membuka_halaman_statistik(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/statistics')
            ->assertOk()
            ->assertSee('Statistik Madrasah')
            ->assertSeeLivewire(StatisticManager::class);
    }

    public function test_keempat_jenis_statistik_otomatis_tersedia(): void
    {
        $this->assertDatabaseCount('statistics', 0);

        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $this->assertDatabaseCount('statistics', 4);

        foreach (['Siswa', 'Guru', 'Tenaga Kependidikan', 'Kelas'] as $label) {
            $this->assertDatabaseHas('statistics', ['label' => $label]);
        }
    }

    public function test_membuka_komponen_berulang_tidak_menggandakan_data(): void
    {
        Livewire::actingAs($this->admin())->test(StatisticManager::class);
        Livewire::actingAs($this->admin())->test(StatisticManager::class);
        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $this->assertDatabaseCount('statistics', 4);
    }

    public function test_admin_bisa_mengubah_angka(): void
    {
        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $statistic = Statistic::where('label', 'Guru')->firstOrFail();

        Livewire::actingAs($this->admin())
            ->test(StatisticManager::class)
            ->call('edit', $statistic->id)
            ->assertSet('showEditModal', true)
            ->assertSet('editingLabel', 'Guru')
            ->set('value', '35')
            ->set('icon', 'graduation-cap')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false)
            ->assertDispatched('toast');

        $this->assertDatabaseHas('statistics', [
            'id' => $statistic->id,
            'label' => 'Guru',
            'value' => 35,
        ]);
    }

    public function test_angka_wajib_diisi(): void
    {
        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $statistic = Statistic::where('label', 'Siswa')->firstOrFail();

        Livewire::actingAs($this->admin())
            ->test(StatisticManager::class)
            ->call('edit', $statistic->id)
            ->set('value', '')
            ->call('save')
            ->assertHasErrors(['value' => 'required']);
    }

    public function test_angka_harus_berupa_bilangan_bulat_positif(): void
    {
        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $statistic = Statistic::where('label', 'Siswa')->firstOrFail();

        Livewire::actingAs($this->admin())
            ->test(StatisticManager::class)
            ->call('edit', $statistic->id)
            ->set('value', '-5')
            ->call('save')
            ->assertHasErrors(['value' => 'min']);
    }

    public function test_label_tidak_ikut_berubah_saat_disimpan(): void
    {
        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $statistic = Statistic::where('label', 'Kelas')->firstOrFail();

        Livewire::actingAs($this->admin())
            ->test(StatisticManager::class)
            ->call('edit', $statistic->id)
            ->set('value', '18')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('statistics', [
            'id' => $statistic->id,
            'label' => 'Kelas',
            'value' => 18,
        ]);
    }

    public function test_daftar_ditampilkan_sesuai_urutan_tetap(): void
    {
        Livewire::actingAs($this->admin())->test(StatisticManager::class);

        $this->actingAs($this->admin())
            ->get('/admin/statistics')
            ->assertOk()
            ->assertSeeInOrder(['Siswa', 'Guru', 'Tenaga Kependidikan', 'Kelas']);
    }
}
