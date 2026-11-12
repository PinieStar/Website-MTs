<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Contracts\View\View;

class PublicGalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::query()
            ->with(['images.media'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('public.galeri.index', [
            'galleries' => $galleries,
        ]);
    }
}
