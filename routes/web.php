<?php

use App\Http\Controllers\Admin\PostInlineImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAchievementController;
use App\Http\Controllers\PublicGalleryController;
use App\Http\Controllers\PublicHomeController;
use App\Http\Controllers\PublicPostController;
use App\Models\Achievement;
use App\Models\Gallery;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicHomeController::class, 'index'])->name('public.home');
Route::view('/profil', 'public.profil')->name('public.profil');
Route::view('/akademik', 'public.akademik')->name('public.akademik');
Route::view('/kontak', 'public.kontak')->name('public.kontak');

/*
 * Halaman publik prestasi siswa & madrasah.
 */
Route::get('/prestasi', [PublicAchievementController::class, 'index'])->name('public.prestasi.index');

/*
 * Halaman publik berita & artikel.
 */
Route::get('/berita', [PublicPostController::class, 'beritaIndex'])->name('public.berita.index');
Route::get('/berita/{slug}', [PublicPostController::class, 'beritaShow'])->name('public.berita.show');
Route::get('/artikel', [PublicPostController::class, 'artikelIndex'])->name('public.artikel.index');
Route::get('/artikel/{slug}', [PublicPostController::class, 'artikelShow'])->name('public.artikel.show');

/*
 * Halaman publik galeri foto.
 */
Route::get('/galeri', [PublicGalleryController::class, 'index'])->name('public.galeri.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();

            $tipeBisaDiakses = collect(['berita', 'artikel'])
                ->filter(fn (string $tipe): bool => $user->canAccessModule($tipe))
                ->values()
                ->all();

            return view('admin.dashboard', [
                'jumlahBerita' => Post::where('type', 'berita')->count(),
                'jumlahArtikel' => Post::where('type', 'artikel')->count(),
                'jumlahGaleri' => Gallery::count(),
                'jumlahPrestasi' => Achievement::count(),
                'tipeBisaDiakses' => $tipeBisaDiakses,
                'draftTerbaru' => Post::where('status', 'draft')
                    ->whereIn('type', $tipeBisaDiakses)
                    ->latest('updated_at')
                    ->limit(5)
                    ->get(),
            ]);
        })->name('dashboard');

        Route::view('/posts/berita', 'admin.posts.index', ['type' => 'berita'])
            ->middleware('module_access:berita')
            ->name('posts.berita');
        Route::view('/posts/artikel', 'admin.posts.index', ['type' => 'artikel'])
            ->middleware('module_access:artikel')
            ->name('posts.artikel');
        Route::post('/posts/inline-image', [PostInlineImageController::class, 'store'])
            ->middleware('module_access:berita,artikel')
            ->name('posts.inline-image');

        Route::middleware('module_access:galeri')->group(function () {
            Route::view('/galleries', 'admin.galleries.index')->name('galleries.index');
            Route::get('/galleries/{gallery}', fn (Gallery $gallery) => view('admin.galleries.index', [
                'galleryId' => $gallery->id,
            ]))->name('galleries.show');
        });

        Route::view('/achievements', 'admin.achievements.index')
            ->middleware('module_access:prestasi')
            ->name('achievements.index');

        Route::view('/statistics', 'admin.statistics.index')
            ->middleware('module_access:statistik')
            ->name('statistics.index');

        /*
         * Khusus Super Admin — dilindungi middleware `super_admin`
         * (EnsureUserIsSuperAdmin) di luar middleware `auth`.
         */
        Route::middleware('super_admin')
            ->prefix('manajemen-admin')
            ->name('manajemen-admin.')
            ->group(function () {
                Route::view('/', 'admin.manajemen-admin.index')->name('index');
            });
    });
});

require __DIR__.'/auth.php';
