<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Contracts\View\View;

class PublicAchievementController extends Controller
{
    /**
     * Urutan tingkat dari yang paling tinggi untuk pengurutan daftar.
     *
     * @var list<string>
     */
    private const TINGKAT_RANK = ['nasional', 'provinsi', 'kabupaten', 'kecamatan', 'madrasah'];

    public function index(): View
    {
        $items = Achievement::query()->get()->sort(function (Achievement $a, Achievement $b): int {
            $year = $b->tahun <=> $a->tahun;
            if ($year !== 0) {
                return $year;
            }

            $rank = array_search($a->tingkat, self::TINGKAT_RANK, true);
            $otherRank = array_search($b->tingkat, self::TINGKAT_RANK, true);
            $rank = $rank === false ? PHP_INT_MAX : $rank;
            $otherRank = $otherRank === false ? PHP_INT_MAX : $otherRank;

            if ($rank !== $otherRank) {
                return $rank <=> $otherRank;
            }

            return $a->id <=> $b->id;
        })->values();

        $years = $items->pluck('tahun')->unique()->sortDesc()->values();

        return view('public.prestasi', [
            'items' => $items,
            'total' => $items->count(),
            'years' => $years,
        ]);
    }
}
