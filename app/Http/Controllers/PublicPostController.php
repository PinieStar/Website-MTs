<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class PublicPostController extends Controller
{
    public function beritaIndex(): View
    {
        return view('public.berita.index', [
            'posts' => $this->postsForType('berita'),
        ]);
    }

    public function artikelIndex(): View
    {
        return view('public.artikel.index', [
            'posts' => $this->postsForType('artikel'),
        ]);
    }

    public function beritaShow(string $slug): View
    {
        return view('public.berita.show', [
            'post' => $this->postForType('berita', $slug),
        ]);
    }

    public function artikelShow(string $slug): View
    {
        return view('public.artikel.show', [
            'post' => $this->postForType('artikel', $slug),
        ]);
    }

    private function postsForType(string $type): LengthAwarePaginator
    {
        return Post::query()
            ->with('media')
            ->published()
            ->type($type)
            ->orderByDesc('sematkan')
            ->orderByDesc('tanggal_publish')
            ->orderByDesc('id')
            ->paginate(9);
    }

    private function postForType(string $type, string $slug): Post
    {
        return Post::query()
            ->with(['media', 'penulis'])
            ->published()
            ->type($type)
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
