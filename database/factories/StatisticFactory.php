<?php

namespace Database\Factories;

use App\Models\Statistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Statistic>
 */
class StatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->randomElement(['Siswa', 'Guru', 'Kelas', 'Tenaga Kependidikan']),
            'value' => fake()->numberBetween(1, 500),
            'icon' => fake()->randomElement(array_keys(Statistic::ICONS)),
            'urutan' => fake()->numberBetween(1, 10),
        ];
    }
}
