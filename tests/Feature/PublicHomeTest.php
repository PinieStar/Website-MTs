<?php

namespace Tests\Feature;

use App\Models\Statistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_menampilkan_angka_dari_database(): void
    {
        Statistic::factory()->create([
            'label' => 'Siswa',
            'value' => 612,
            'icon' => 'users',
            'urutan' => 1,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Siswa')
            ->assertSee('612')
            ->assertSee('Madrasah dalam angka');
    }

    public function test_beranda_memakai_nilai_bawaan_saat_tabel_kosong(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee((string) config('site.stats.students'))
            ->assertSee('Tenaga Kependidikan');
    }
}
