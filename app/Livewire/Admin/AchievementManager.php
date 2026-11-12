<?php

namespace App\Livewire\Admin;

use App\Models\Achievement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AchievementManager extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | Filter daftar
    |--------------------------------------------------------------------------
    */

    public string $filterTingkat = '';

    public string $filterBidang = '';

    public string $filterTahun = '';

    /*
    |--------------------------------------------------------------------------
    | Form tambah/edit
    |--------------------------------------------------------------------------
    */

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $judul = '';

    public string $peringkat = '';

    public string $tingkat = '';

    public string $bidang = '';

    public string $tahun = '';

    public string $penyelenggara = '';

    /*
    |--------------------------------------------------------------------------
    | Modal hapus
    |--------------------------------------------------------------------------
    */

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingJudul = '';

    /**
     * Lindungi setiap request Livewire: hanya akun dengan akses modul prestasi yang boleh.
     */
    public function boot(): void
    {
        Gate::authorize('access-module', 'prestasi');
    }

    /**
     * Reset halaman saat filter berubah.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['filterTingkat', 'filterBidang', 'filterTahun'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Aturan validasi form.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'peringkat' => ['required', 'string', 'max:100'],
            'tingkat' => ['required', Rule::in(array_keys(Achievement::TINGKAT))],
            'bidang' => ['required', Rule::in(array_keys(Achievement::BIDANG))],
            'tahun' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
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
            'judul.required' => 'Judul prestasi wajib diisi.',
            'judul.max' => 'Judul prestasi maksimal 255 karakter.',
            'peringkat.required' => 'Peringkat wajib diisi, misalnya "Juara 1".',
            'peringkat.max' => 'Peringkat maksimal 100 karakter.',
            'tingkat.required' => 'Tingkat perlombaan wajib dipilih.',
            'tingkat.in' => 'Tingkat perlombaan tidak valid.',
            'bidang.required' => 'Bidang perlombaan wajib dipilih.',
            'bidang.in' => 'Bidang perlombaan tidak valid.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.integer' => 'Tahun harus berupa angka.',
            'tahun.min' => 'Tahun tidak valid.',
            'tahun.max' => 'Tahun tidak boleh melebihi tahun depan.',
            'penyelenggara.max' => 'Nama penyelenggara maksimal 255 karakter.',
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
            'judul' => 'judul',
            'peringkat' => 'peringkat',
            'tingkat' => 'tingkat',
            'bidang' => 'bidang',
            'tahun' => 'tahun',
            'penyelenggara' => 'penyelenggara',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $achievement = Achievement::findOrFail($id);

        $this->editingId = $achievement->id;
        $this->judul = $achievement->judul;
        $this->peringkat = $achievement->peringkat;
        $this->tingkat = $achievement->tingkat;
        $this->bidang = $achievement->bidang;
        $this->tahun = (string) $achievement->tahun;
        $this->penyelenggara = $achievement->penyelenggara ?? '';

        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            Achievement::findOrFail($this->editingId)->update($validated);
            $message = 'Prestasi berhasil diperbarui.';
        } else {
            Achievement::create($validated);
            $message = 'Prestasi berhasil ditambahkan.';
        }

        $this->closeForm();
        $this->resetPage();
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $achievement = Achievement::findOrFail($id);

        $this->deletingId = $achievement->id;
        $this->deletingJudul = $achievement->judul;
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

        Achievement::find($this->deletingId)?->delete();

        $this->cancelDelete();
        $this->dispatch('toast', message: 'Prestasi berhasil dihapus.', type: 'success');
    }

    public function resetFilters(): void
    {
        $this->reset(['filterTingkat', 'filterBidang', 'filterTahun']);
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
        $achievements = Achievement::query()
            ->when($this->filterTingkat !== '', fn ($query) => $query->where('tingkat', $this->filterTingkat))
            ->when($this->filterBidang !== '', fn ($query) => $query->where('bidang', $this->filterBidang))
            ->when($this->filterTahun !== '', fn ($query) => $query->where('tahun', (int) $this->filterTahun))
            ->latest('created_at')
            ->latest('id')
            ->paginate(10);

        $tahunOptions = Achievement::query()
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('livewire.admin.achievement-manager', [
            'achievements' => $achievements,
            'tahunOptions' => $tahunOptions,
            'tingkatOptions' => Achievement::TINGKAT,
            'bidangOptions' => Achievement::BIDANG,
        ]);
    }

    /**
     * Kembalikan form ke kondisi awal.
     */
    private function resetForm(): void
    {
        $this->reset([
            'editingId',
            'judul',
            'peringkat',
            'tingkat',
            'bidang',
            'tahun',
            'penyelenggara',
        ]);

        $this->tahun = (string) now()->year;
        $this->resetValidation();
    }
}
