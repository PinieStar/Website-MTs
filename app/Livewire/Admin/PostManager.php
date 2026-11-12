<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PostManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Filter daftar
    |--------------------------------------------------------------------------
    */

    public string $filterType = '';

    public string $filterStatus = '';

    public string $search = '';

    /**
     * Tipe post yang tetap untuk halaman admin ini (berita atau artikel).
     */
    public string $pageType = 'berita';

    /*
    |--------------------------------------------------------------------------
    | Form tambah/edit
    |--------------------------------------------------------------------------
    */

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $type = 'berita';

    public string $judul = '';

    public string $slug = '';

    public string $ringkasan = '';

    public string $isi = '';

    public string $kategori = '';

    public string $status = 'draft';

    public bool $sematkan = false;

    public string $tanggal_publish = '';

    public string $penulis_nama = '';

    /** @var TemporaryUploadedFile|null */
    public $sampul = null;

    public bool $removeSampul = false;

    public string $existingSampulUrl = '';

    public bool $slugTouched = false;

    private bool $autoFillingSlug = false;

    /*
    |--------------------------------------------------------------------------
    | Modal hapus
    |--------------------------------------------------------------------------
    */

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingJudul = '';

    /**
     * Tetapkan tipe post sesuai halaman admin yang dibuka.
     */
    public function mount(string $type = 'berita'): void
    {
        $this->pageType = array_key_exists($type, Post::TYPE) ? $type : 'berita';
        Gate::authorize('access-module', $this->pageType);
        $this->filterType = $this->pageType;
        $this->type = $this->pageType;
    }

    /**
     * Cek akses di setiap request Livewire berikutnya (bukan hanya saat halaman dibuka).
     * Berita dan Artikel adalah modul terpisah, jadi dicek sesuai tipe halamannya.
     */
    public function hydrate(): void
    {
        Gate::authorize('access-module', $this->pageType);
    }

    /**
     * Reset halaman saat filter atau pencarian berubah.
     */
    public function updated(string $property, mixed $value = null): void
    {
        if (in_array($property, ['filterType', 'filterStatus', 'search'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Tipe berita/artikel memengaruhi field kategori.
     */
    public function updatedType(): void
    {
        if ($this->type !== 'berita') {
            $this->kategori = '';
        }

        $this->resetValidation('kategori');
    }

    /**
     * Isi slug otomatis saat judul diisi pada mode tambah.
     */
    public function updatedJudul(): void
    {
        if ($this->editingId !== null || $this->slugTouched) {
            return;
        }

        $this->autoFillingSlug = true;
        $this->slug = Str::slug($this->judul);
        $this->autoFillingSlug = false;
    }

    /**
     * Tandai slug sudah diedit manual agar tidak ditimpa judul.
     */
    public function updatedSlug(): void
    {
        if ($this->autoFillingSlug) {
            return;
        }

        $this->slugTouched = true;
        $normalized = Str::slug($this->slug);

        if ($normalized !== $this->slug) {
            $this->autoFillingSlug = true;
            $this->slug = $normalized;
            $this->autoFillingSlug = false;
        }
    }

    public function updatedSampul(): void
    {
        $this->removeSampul = false;
        $this->resetValidation('sampul');
    }

    /**
     * Buat slug dari judul secara manual.
     */
    public function generateSlugFromJudul(): void
    {
        if (blank($this->judul)) {
            return;
        }

        $this->slug = Str::slug($this->judul);
        $this->slugTouched = false;
        $this->resetValidation('slug');
    }

    /**
     * Aturan validasi form.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(Post::TYPE))],
            'judul' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:500'],
            'isi' => [
                'required',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (mb_strlen(trim(strip_tags((string) $value))) < 50) {
                        $fail('Isi artikel minimal 50 karakter.');
                    }
                },
            ],
            'kategori' => [
                Rule::requiredIf($this->type === 'berita'),
                'nullable',
                Rule::in(array_keys(Post::KATEGORI)),
            ],
            'status' => ['required', Rule::in(array_keys(Post::STATUS))],
            'tanggal_publish' => ['required', 'date'],
            'penulis_nama' => ['required', 'string', 'max:255'],
            'sematkan' => ['boolean'],
            'sampul' => [
                'nullable',
                'image',
                'mimes:'.implode(',', config('cms.image.extensions')),
                'max:'.config('cms.image.max_kb'),
            ],
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
            'type.required' => 'Jenis post wajib dipilih.',
            'type.in' => 'Jenis post tidak valid.',
            'judul.required' => 'Judul wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'slug.max' => 'Alamat halaman maksimal 255 karakter.',
            'ringkasan.max' => 'Ringkasan maksimal 500 karakter.',
            'isi.required' => 'Isi artikel wajib diisi.',
            'kategori.required' => 'Kategori wajib dipilih untuk berita.',
            'kategori.in' => 'Kategori tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
            'tanggal_publish.required' => 'Tanggal tayang wajib diisi.',
            'tanggal_publish.date' => 'Tanggal tayang tidak valid.',
            'penulis_nama.required' => 'Nama penulis wajib diisi.',
            'penulis_nama.max' => 'Nama penulis maksimal 255 karakter.',
            'sampul.image' => 'File sampul harus berupa gambar.',
            'sampul.mimes' => 'Format gambar sampul harus JPG.',
            'sampul.max' => 'Ukuran gambar sampul maksimal '.(config('cms.image.max_kb') / 1024).' MB.',
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
            'type' => 'jenis post',
            'judul' => 'judul',
            'slug' => 'alamat halaman',
            'ringkasan' => 'ringkasan',
            'isi' => 'isi artikel',
            'kategori' => 'kategori',
            'status' => 'status',
            'tanggal_publish' => 'tanggal tayang',
            'penulis_nama' => 'nama penulis',
            'sampul' => 'gambar sampul',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $post = Post::findOrFail($id);

        $this->editingId = $post->id;
        $this->type = $post->type;
        $this->judul = $post->judul;
        $this->slug = $post->slug;
        $this->ringkasan = $post->ringkasan ?? '';
        $this->isi = $post->sanitizedIsi();
        $this->kategori = $post->kategori ?? '';
        $this->status = $post->status;
        $this->sematkan = $post->sematkan;
        $this->tanggal_publish = $post->tanggal_publish?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->penulis_nama = $post->penulis_nama ?? '';
        $this->existingSampulUrl = $post->getFirstMediaUrl('sampul');
        $this->sampul = null;
        $this->removeSampul = false;
        $this->slugTouched = true;

        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->type = $this->pageType;

        $validated = $this->validate();

        unset($validated['sampul']);

        $validated['isi'] = Post::sanitizeHtml($validated['isi']);

        if (filled($validated['slug'] ?? null)) {
            $validated['slug'] = $this->ensureUniqueSlug($validated['slug'], $this->editingId);
        }

        $validated['kategori'] = $this->type === 'berita' ? ($validated['kategori'] ?? null) : null;
        $validated['sematkan'] = (bool) ($validated['sematkan'] ?? false);
        $validated['penulis_nama'] = filled($validated['penulis_nama'] ?? null)
            ? trim((string) $validated['penulis_nama'])
            : null;

        if ($this->editingId) {
            $post = Post::findOrFail($this->editingId);
            $post->fill($validated);

            if (blank($post->slug)) {
                $post->generateSlug();
            }

            $post->save();
            $message = 'Post berhasil diperbarui.';
        } else {
            $validated['penulis_id'] = auth()->id();
            $post = Post::create($validated);
            $message = 'Post berhasil dibuat.';
        }

        if ($this->removeSampul) {
            $post->clearMediaCollection('sampul');
        }

        if ($this->sampul) {
            $post->addMedia($this->sampul->getRealPath())
                ->usingFileName($this->sampul->getClientOriginalName())
                ->toMediaCollection('sampul');
        }

        $this->closeForm();
        $this->resetPage();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function hapusSampul(): void
    {
        $this->sampul = null;
        $this->removeSampul = true;
        $this->existingSampulUrl = '';
        $this->resetValidation('sampul');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $post = Post::findOrFail($id);

        $this->deletingId = $post->id;
        $this->deletingJudul = $post->judul;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->deletingJudul = '';
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $post = Post::find($this->deletingId);

        if ($post) {
            $this->deleteInlineImages($post->isi);
            $post->clearMediaCollection('sampul');
            $post->delete();
        }

        $this->cancelDelete();
        $this->dispatch('toast', message: 'Post berhasil dihapus.', type: 'success');
    }

    public function resetFilters(): void
    {
        $this->reset(['filterStatus', 'search']);
        $this->filterType = $this->pageType;
        $this->resetPage();
    }

    /**
     * Pakai tampilan pagination bertema situs untuk komponen Livewire ini.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.site-livewire';
    }

    public function render(): View
    {
        $posts = Post::query()
            ->with(['media', 'penulis'])
            ->when($this->filterType !== '', fn ($query) => $query->where('type', $this->filterType))
            ->when($this->filterStatus !== '', fn ($query) => $query->where('status', $this->filterStatus))
            ->when($this->search !== '', fn ($query) => $query->where('judul', 'like', '%'.$this->search.'%'))
            ->orderByDesc('sematkan')
            ->orderByDesc('tanggal_publish')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.post-manager', [
            'posts' => $posts,
            'statusOptions' => Post::STATUS,
            'kategoriOptions' => Post::KATEGORI,
        ]);
    }

    /**
     * Pastikan slug unik; tambahkan akhiran angka bila bentrok.
     */
    private function ensureUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug);

        if ($base === '') {
            return '';
        }

        $candidate = $base;
        $suffix = 1;

        while (Post::query()
            ->where('slug', $candidate)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $candidate = $base.'-'.$suffix++;
        }

        return $candidate;
    }

    /**
     * Hapus gambar inline yang diunggah lewat editor TipTap.
     */
    private function deleteInlineImages(string $html): void
    {
        if (! preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches)) {
            return;
        }

        foreach (array_unique($matches[1]) as $source) {
            $path = parse_url($source, PHP_URL_PATH) ?: $source;

            if (! Str::startsWith($path, '/storage/posts/inline/')) {
                continue;
            }

            Storage::disk('public')->delete(Str::after($path, '/storage/'));
        }
    }

    /**
     * Kembalikan form ke kondisi awal.
     */
    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'type',
            'judul',
            'slug',
            'ringkasan',
            'isi',
            'kategori',
            'status',
            'sematkan',
            'tanggal_publish',
            'penulis_nama',
            'removeSampul',
            'existingSampulUrl',
            'slugTouched',
        ]);

        $this->sampul = null;
        $this->type = $this->pageType;
        $this->status = 'draft';
        $this->tanggal_publish = now()->format('Y-m-d');
        $this->slugTouched = false;
        $this->resetValidation();
    }
}
