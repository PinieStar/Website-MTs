<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostInlineImageController extends Controller
{
    /**
     * Unggah gambar yang disisipkan lewat editor TipTap.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:'.implode(',', config('cms.image.extensions')),
                'max:'.config('cms.image.max_kb'),
            ],
        ], [
            'image.required' => 'Pilih gambar terlebih dahulu.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPG.',
            'image.max' => 'Ukuran gambar maksimal '.(config('cms.image.max_kb') / 1024).' MB.',
        ]);

        $path = $validated['image']->store('posts/inline', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
        ]);
    }
}
