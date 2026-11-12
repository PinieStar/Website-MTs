<?php

namespace Database\Seeders;

use App\Models\Statistic;
use Illuminate\Database\Seeder;

class StatisticSeeder extends Seeder
{
    /**
     * Seed data "Statistik Madrasah" dari nilai bawaan situs.
     */
    public function run(): void
    {
        foreach (Statistic::defaults() as $statistic) {
            Statistic::query()->updateOrCreate(
                ['label' => $statistic->label],
                [
                    'value' => $statistic->value,
                    'icon' => $statistic->icon,
                    'urutan' => $statistic->urutan,
                ],
            );
        }
    }
}
