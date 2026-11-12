<?php

namespace App\Livewire\Admin;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GalleryManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Album terpilih
    |--------------------------------------------------------------------------
    */

    public ?int $galleryId = null;

    /*
    |--------------------------------------------------------------------------
    | Form tambah/edit album
    |--------------------------------------------------------------------------
    */

    public bool $showAlbumForm = false;

    public ?int $editingAlbumId = null;

    public string $album = '';

    public string $keterangan = '';

    /*
    |--------------------------------------------------------------------------
    | Upload foto
    |--------------------------------------------------------------------------
    */

    /** @var array<int, UploadedFile> */
    public array $photos = [];

    /*
    |--------------------------------------------------------------------------
    | Modal hapus album
    |--------------------------------------------------------------------------
    */

    public bool $showDeleteAlbumModal = false;

    public ?int $deletingAlbumId = null;

    public string $deletingAlbumName = '';

    public int $deletingAlbumPhotoCount = 0;

    /*
    |--------------------------------------------------------------------------
    | Modal hapus foto
    |--------------------------------------------------------------------------
    */

    public bool $showDeletePhotoModal = false;

    public ?int $deletingPhotoId = null;

    public string $deletingPhotoAlbumName = '';

    public string $deletingPhotoUrl = '';

    /**
     * Lindungi setiap request Livewire: hanya akun dengan akses modul galeri yang boleh.
     */
    public function boot(): void
    {
        Gate::authorize('access-module', 'galeri');
    }

    /**
     * Buka album tertentu saat halaman /admin/galleries/{id} diakses.
     */
    public function mount(?int $galleryId = null): void
    {
        if ($galleryId !== null) {
            abort_unless(Gallery::whereKey($galleryId)->exists(), 404);

            $this->galleryId = $galleryId;
        }
    }

    /**
     * Aturan validasi form album.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'album' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'album.required' => 'Nama album wajib diisi.',
            'album.max' => 'Nama album maksimal 255 karakter.',
            'keterangan.max' => 'Keterangan album maksimal 1.000 karakter.',
        ];
    }

    /**
     * Nama atribut yang dipakai di pesan validasi.
     *
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'album' => 'nama album',
            'keterangan' => 'keterangan',
        ];
    }

    /**
     * Buka form album baru.
     */
    public function createAlbum(): void
    {
        $this->resetAlbumForm();
        $this->showAlbumForm = true;
    }

    /**
     * Buka form edit album.
     */
    public function editAlbum(int $id): void
    {
        $gallery = Gallery::findOrFail($id);

        $this->editingAlbumId = $gallery->id;
        $this->album = $gallery->album;
        $this->keterangan = $gallery->keterangan ?? '';

        $this->resetValidation();
        $this->showAlbumForm = true;
    }

    /**
     * Simpan album baru atau perubahan album.
     */
    public function saveAlbum(): void
    {
        $validated = $this->validate();

        if ($this->editingAlbumId) {
            Gallery::findOrFail($this->editingAlbumId)->update($validated);
            $message = 'Album berhasil diperbarui.';
        } else {
            $gallery = Gallery::create($validated);
            $this->galleryId = $gallery->id;
            $message = 'Album berhasil dibuat. Silakan unggah foto.';
        }

        $this->closeAlbumForm();
        $this->resetPage();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    /**
     * Tutup form album.
     */
    public function closeAlbumForm(): void
    {
        $this->showAlbumForm = false;
        $this->resetAlbumForm();
    }

    /**
     * Validasi file foto yang baru dipilih.
     */
    public function updatedPhotos(): void
    {
        $this->validatePhotos();
    }

    /**
     * Hapus satu file dari daftar antrean upload.
     */
    public function removePendingPhoto(int $index): void
    {
        if (! array_key_exists($index, $this->photos)) {
            return;
        }

        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);

        $this->validatePhotos();

        if ($this->photos === []) {
            $this->dispatch('photos-reset');
        }
    }

    /**
     * Bersihkan seluruh antrean upload.
     */
    public function clearPendingPhotos(): void
    {
        $this->photos = [];
        $this->resetValidation();
        $this->dispatch('photos-reset');
    }

    /**
     * Simpan seluruh foto yang dipilih ke album aktif.
     */
    public function uploadPhotos(): void
    {
        if (! $this->galleryId) {
            return;
        }

        if ($this->photos === []) {
            $this->addError('photos', 'Pilih minimal satu foto untuk diunggah.');

            return;
        }

        if (! $this->validatePhotos()) {
            $this->dispatch(
                'toast',
                message: 'Ada file yang gagal divalidasi. Periksa pesan error pada masing-masing foto.',
                type: 'danger',
            );

            return;
        }

        $gallery = Gallery::findOrFail($this->galleryId);
        $nextOrder = ((int) $gallery->images()->max('urutan')) + 1;
        $uploaded = 0;

        foreach ($this->photos as $photo) {
            if (! $photo instanceof UploadedFile) {
                continue;
            }

            $image = $gallery->images()->create([
                'urutan' => $nextOrder++,
            ]);

            $image->addMedia($photo->getRealPath())
                ->usingFileName($photo->getClientOriginalName())
                ->toMediaCollection('foto');

            $uploaded++;
        }

        $this->photos = [];
        $this->resetValidation();
        $this->dispatch('photos-reset');

        if ($uploaded > 0) {
            $this->dispatch(
                'toast',
                message: $uploaded.' foto berhasil diunggah.',
                type: 'success',
            );
        }
    }

    /**
     * Pindahkan foto satu posisi ke atas.
     */
    public function movePhotoUp(int $id): void
    {
        $this->movePhoto($id, -1);
    }

    /**
     * Pindahkan foto satu posisi ke bawah.
     */
    public function movePhotoDown(int $id): void
    {
        $this->movePhoto($id, 1);
    }

    /**
     * Buka konfirmasi hapus album.
     */
    public function confirmDeleteAlbum(int $id): void
    {
        $gallery = Gallery::query()->withCount('images')->findOrFail($id);

        $this->deletingAlbumId = $gallery->id;
        $this->deletingAlbumName = $gallery->album;
        $this->deletingAlbumPhotoCount = $gallery->images_count;
        $this->showDeleteAlbumModal = true;
    }

    /**
     * Batalkan hapus album.
     */
    public function cancelDeleteAlbum(): void
    {
        $this->showDeleteAlbumModal = false;
        $this->deletingAlbumId = null;
        $this->deletingAlbumName = '';
        $this->deletingAlbumPhotoCount = 0;
    }

    /**
     * Hapus album beserta seluruh foto dan file storage-nya.
     */
    public function deleteAlbum(): void
    {
        if (! $this->deletingAlbumId) {
            return;
        }

        $deletedId = $this->deletingAlbumId;
        $gallery = Gallery::query()->with('images.media')->find($deletedId);

        if ($gallery) {
            foreach ($gallery->images as $image) {
                $image->clearMediaCollection('foto');
                $image->delete();
            }

            $gallery->delete();
        }

        $this->cancelDeleteAlbum();

        if ($this->galleryId === $deletedId) {
            $this->galleryId = null;
            $this->redirectRoute('admin.galleries.index');

            return;
        }

        $this->resetPage();
        $this->dispatch(
            'toast',
            message: 'Album dan seluruh fotonya berhasil dihapus.',
            type: 'success',
        );
    }

    /**
     * Buka konfirmasi hapus satu foto.
     */
    public function confirmDeletePhoto(int $id): void
    {
        $photo = GalleryImage::query()->with(['gallery', 'media'])->findOrFail($id);

        if ($this->galleryId === null || $photo->gallery_id !== $this->galleryId) {
            abort(404);
        }

        $this->deletingPhotoId = $photo->id;
        $this->deletingPhotoAlbumName = $photo->gallery?->album ?? '';
        $this->deletingPhotoUrl = $photo->getFirstMediaUrl('foto');
        $this->showDeletePhotoModal = true;
    }

    /**
     * Batalkan hapus foto.
     */
    public function cancelDeletePhoto(): void
    {
        $this->showDeletePhotoModal = false;
        $this->deletingPhotoId = null;
        $this->deletingPhotoAlbumName = '';
        $this->deletingPhotoUrl = '';
    }

    /**
     * Hapus satu foto tanpa menghapus album.
     */
    public function deletePhoto(): void
    {
        if (! $this->deletingPhotoId) {
            return;
        }

        $photo = GalleryImage::query()->with('media')->find($this->deletingPhotoId);

        if ($photo) {
            if ($this->galleryId === null || $photo->gallery_id !== $this->galleryId) {
                abort(404);
            }

            $galleryId = $photo->gallery_id;

            $photo->clearMediaCollection('foto');
            $photo->delete();
            $this->resequencePhotos($galleryId);
        }

        $this->cancelDeletePhoto();
        $this->dispatch('toast', message: 'Foto berhasil dihapus.', type: 'success');
    }

    /**
     * Pakai tampilan pagination bertema situs untuk komponen Livewire ini.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.site-livewire';
    }

    /**
     * Tampilkan daftar album atau kelola foto satu album.
     */
    public function render(): View
    {
        $selectedGallery = null;

        if ($this->galleryId) {
            $selectedGallery = Gallery::query()
                ->with(['images.media'])
                ->withCount('images')
                ->find($this->galleryId);

            if (! $selectedGallery) {
                $this->galleryId = null;
            }
        }

        $galleries = $selectedGallery ? null : $this->galleries();

        return view('livewire.admin.gallery-manager', [
            'selectedGallery' => $selectedGallery,
            'galleries' => $galleries,
        ]);
    }

    /**
     * Daftar album beserta cover dan jumlah foto.
     */
    private function galleries(): LengthAwarePaginator
    {
        return Gallery::query()
            ->with(['images.media'])
            ->withCount('images')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(9);
    }

    /**
     * Pindahkan foto berdasarkan arah -1 (naik) atau 1 (turun).
     */
    private function movePhoto(int $id, int $direction): void
    {
        if (! $this->galleryId) {
            return;
        }

        $photos = GalleryImage::query()
            ->where('gallery_id', $this->galleryId)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $photos->search(fn (GalleryImage $photo): bool => $photo->id === $id);

        if ($index === false) {
            return;
        }

        $target = $index + $direction;

        if ($target < 0 || $target >= $photos->count()) {
            return;
        }

        $ordered = $photos->all();
        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        DB::transaction(function () use ($ordered): void {
            foreach ($ordered as $position => $photo) {
                $photo->update(['urutan' => $position + 1]);
            }
        });
    }

    /**
     * Rapikan kembali nomor urut foto setelah ada yang dihapus.
     */
    private function resequencePhotos(int $galleryId): void
    {
        $photos = GalleryImage::query()
            ->where('gallery_id', $galleryId)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        DB::transaction(function () use ($photos): void {
            foreach ($photos as $position => $photo) {
                if ($photo->urutan !== $position + 1) {
                    $photo->update(['urutan' => $position + 1]);
                }
            }
        });
    }

    /**
     * Validasi setiap file foto dengan pesan yang menyebut nama file.
     */
    private function validatePhotos(): bool
    {
        $this->resetValidation();

        $valid = true;
        $allowedExtensions = config('cms.image.extensions');
        $allowedMimeTypes = config('cms.image.mime_types');
        $maxBytes = config('cms.image.max_kb') * 1024;
        $maxMegabytes = config('cms.image.max_kb') / 1024;

        foreach ($this->photos as $index => $photo) {
            if (! $photo instanceof UploadedFile) {
                continue;
            }

            $name = $photo->getClientOriginalName();
            $extension = strtolower($photo->getClientOriginalExtension());
            $mimeType = (string) $photo->getMimeType();

            if (! in_array($extension, $allowedExtensions, true)
                || ! in_array($mimeType, $allowedMimeTypes, true)) {
                $this->addError(
                    'photos.'.$index,
                    '"'.$name.'" formatnya tidak didukung. Gunakan JPG.',
                );
                $valid = false;

                continue;
            }

            if ($photo->getSize() > $maxBytes) {
                $size = number_format($photo->getSize() / 1024 / 1024, 1, ',', '.');

                $this->addError(
                    'photos.'.$index,
                    '"'.$name.'" terlalu besar ('.$size.' MB), maksimal '.$maxMegabytes.' MB.',
                );
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Kembalikan form album ke kondisi awal.
     */
    private function resetAlbumForm(): void
    {
        $this->reset([
            'editingAlbumId',
            'album',
            'keterangan',
        ]);

        $this->resetValidation();
    }
}
