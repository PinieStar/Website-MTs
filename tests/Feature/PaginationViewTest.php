<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaginationViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagination_publik_memakai_tampilan_situs(): void
    {
        Post::factory()->count(12)->berita()->create();

        $this->get('/berita')
            ->assertOk()
            ->assertSee('Navigasi halaman', false)
            ->assertSee('Berikutnya')
            ->assertSee('aria-disabled="true"', false)
            ->assertSee('bg-brand', false);
    }

    public function test_pagination_admin_memakai_tampilan_yang_sama_dan_bisa_pindah_halaman(): void
    {
        Post::factory()->count(12)->berita()->create();

        Livewire::actingAs(User::factory()->create(['role' => 'admin']))
            ->test(\App\Livewire\Admin\PostManager::class, ['type' => 'berita'])
            ->assertSee('Navigasi halaman', false)
            ->assertSee('Berikutnya')
            ->call('nextPage')
            ->assertSet('paginators.page', 2);
    }
}
