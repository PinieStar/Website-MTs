<?php

namespace App\Livewire\Admin;

use App\Models\Statistic;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class StatisticManager extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Modal ubah angka
    |--------------------------------------------------------------------------
    |
    | Jenis statistik sudah tetap: Siswa, Guru, Tenaga Kependidikan, Kelas.
    | Admin hanya bisa mengubah angkanya lewat modal ini — tidak ada tombol
    | tambah maupun hapus di halaman ini supaya jenis datanya tidak berubah.
    */

    public bool $showEditModal = false;

    public ?int $editingId = null;

    public string $editingLabel = '';

    public string $value = '0';

    public string $icon = 'users';

    /**
     * Lindungi setiap request Livewire: hanya akun dengan akses modul statistik yang boleh.
     */
    public function boot(): void
    {
        Gate::authorize('access-module', 'statistik');
    }

    /**
     * Pastikan keempat jenis statistik selalu tersedia begitu halaman dibuka.
     */
    public function mount(): void
    {
        Statistic::syncDefaults();
    }

    /**
     * Aturan validasi form. Hanya angka dan ikon yang bisa diubah admin.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'value' => ['required', 'integer', 'min:0', 'max:1000000'],
            'icon' => ['required', Rule::in(array_keys(Statistic::ICONS))],
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
            'value.required' => 'Angka statistik wajib diisi.',
            'value.integer' => 'Angka statistik harus berupa bilangan bulat.',
            'value.min' => 'Angka statistik tidak boleh negatif.',
            'value.max' => 'Angka statistik maksimal 1.000.000.',
            'icon.required' => 'Ikon wajib dipilih.',
            'icon.in' => 'Ikon yang dipilih tidak valid.',
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
            'value' => 'angka',
            'icon' => 'ikon',
        ];
    }

    /**
     * Buka modal untuk mengubah angka salah satu statistik yang sudah ada.
     */
    public function edit(int $id): void
    {
        $statistic = Statistic::findOrFail($id);

        $this->editingId = $statistic->id;
        $this->editingLabel = $statistic->label;
        $this->value = (string) $statistic->value;
        $this->icon = $statistic->icon;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    /**
     * Simpan perubahan angka/ikon. Label dan urutan tidak pernah diubah
     * dari sini, supaya jenis statistik tetap empat: Siswa, Guru,
     * Tenaga Kependidikan, dan Kelas.
     */
    public function save(): void
    {
        $validated = $this->validate();

        Statistic::findOrFail($this->editingId)->update($validated);

        $this->closeModal();
        $this->dispatch('toast', message: 'Angka statistik berhasil diperbarui.', type: 'success');
    }

    public function closeModal(): void
    {
        $this->showEditModal = false;
        $this->reset(['editingId', 'editingLabel', 'value', 'icon']);
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.admin.statistic-manager', [
            'statistics' => Statistic::query()->ordered()->get(),
            'iconOptions' => Statistic::ICONS,
        ]);
    }
}
