<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Contracts\View\View;

class PublicHomeController extends Controller
{
    public function index(): View
    {
        $statistics = Statistic::query()->ordered()->get();

        if ($statistics->isEmpty()) {
            $statistics = Statistic::defaults();
        }

        return view('public.home', [
            'statistics' => $statistics,
        ]);
    }
}
