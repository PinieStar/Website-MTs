<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');
        $judul = Str::ucfirst($faker->unique()->sentence(6, false));

        return [
            'type' => 'berita',
            'judul' => $judul,
            'ringkasan' => $faker->paragraph(2),
            'isi' => $this->body($faker->paragraphs(4)),
            'kategori' => $faker->randomElement(array_keys(Post::KATEGORI)),
            'status' => 'publish',
            'sematkan' => false,
            'tanggal_publish' => $faker->dateTimeBetween('-6 months', 'now'),
            'penulis_id' => User::factory(),
        ];
    }

    /**
     * Post berjenis berita.
     */
    public function berita(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'berita',
            'kategori' => fake('id_ID')->randomElement(array_keys(Post::KATEGORI)),
        ]);
    }

    /**
     * Post berjenis artikel.
     */
    public function artikel(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => 'artikel',
            'kategori' => null,
        ]);
    }

    /**
     * Post berstatus draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'draft',
        ]);
    }

    /**
     * Post yang disematkan di daftar.
     */
    public function sematkan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'sematkan' => true,
        ]);
    }

    /**
     * Susun paragraf faker menjadi HTML yang lolos sanitasi.
     *
     * @param  array<int, string>  $paragraf
     */
    private function body(array $paragraf): string
    {
        return collect($paragraf)
            ->map(fn (string $item): string => '<p>'.e($item).'</p>')
            ->implode('');
    }
}
