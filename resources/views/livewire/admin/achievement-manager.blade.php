<div class="mx-auto max-w-6xl space-y-6">

    {{-- Judul halaman + tombol tambah --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-xl font-semibold">Daftar Prestasi</h2>
            <p class="mt-1 text-sm text-ink-soft">
                Kelola data prestasi siswa dan madrasah. Data terbaru otomatis tampil paling atas.
            </p>
        </div>

        <button type="button" wire:click="create"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover">
            <x-admin.icon name="plus" class="h-4 w-4" />
            Tambah Prestasi
        </button>
    </div>

    {{-- Tabel + filter --}}
    <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
        <div class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:flex-wrap sm:items-center">
            <span class="flex items-center gap-2 text-sm font-medium text-ink-soft">
                <x-admin.icon name="filter" class="h-4 w-4" />
                Filter
            </span>

            <select wire:model.live="filterTingkat"
                    class="w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright sm:w-auto">
                <option value="">Semua Tingkat</option>
                @foreach ($tingkatOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterBidang"
                    class="w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright sm:w-auto">
                <option value="">Semua Bidang</option>
                @foreach ($bidangOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterTahun"
                    class="w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright sm:w-auto">
                <option value="">Semua Tahun</option>
                @foreach ($tahunOptions as $tahun)
                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                @endforeach
            </select>

            @if ($filterTingkat || $filterBidang || $filterTahun)
                <button type="button" wire:click="resetFilters"
                        class="text-sm font-medium text-teal-bright hover:underline">
                    Reset filter
                </button>
            @endif

            <span wire:loading wire:target="filterTingkat,filterBidang,filterTahun"
                  class="text-xs text-ink-soft">
                Memuat...
            </span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-sm">
                <thead class="bg-paper text-left text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-5 py-3 font-medium">Judul</th>
                        <th class="px-5 py-3 font-medium">Peringkat</th>
                        <th class="px-5 py-3 font-medium">Tingkat</th>
                        <th class="px-5 py-3 font-medium">Bidang</th>
                        <th class="px-5 py-3 font-medium">Tahun</th>
                        <th class="px-5 py-3 font-medium">Penyelenggara</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($achievements as $achievement)
                        <tr wire:key="achievement-{{ $achievement->id }}" class="odd:bg-bone even:bg-paper/40">
                            <td class="px-5 py-3">
                                <p class="font-medium text-ink">{{ $achievement->judul }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full border border-gold/30 bg-gold/15 px-2.5 py-1 text-xs font-semibold text-gold-text">
                                    {{ $achievement->peringkat }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-ink-soft">{{ $achievement->labelTingkat() }}</td>
                            <td class="px-5 py-3 text-ink-soft">{{ $achievement->labelBidang() }}</td>
                            <td class="px-5 py-3 text-ink-soft">{{ $achievement->tahun }}</td>
                            <td class="px-5 py-3 text-ink-soft">{{ $achievement->penyelenggara ?: '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="edit({{ $achievement->id }})"
                                            title="Edit {{ $achievement->judul }}"
                                            class="rounded-lg p-2 text-teal-bright transition hover:bg-mint">
                                        <x-admin.icon name="pencil" class="h-4 w-4" />
                                        <span class="sr-only">Edit</span>
                                    </button>

                                    <button type="button" wire:click="confirmDelete({{ $achievement->id }})"
                                            title="Hapus {{ $achievement->judul }}"
                                            class="rounded-lg p-2 text-danger-text transition hover:bg-danger/10">
                                        <x-admin.icon name="trash" class="h-4 w-4" />
                                        <span class="sr-only">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-ink-soft">
                                Belum ada data prestasi. Klik <strong>Tambah Prestasi</strong> untuk menambahkan data pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Daftar kartu untuk layar HP --}}
        <div class="divide-y divide-line md:hidden">
            @forelse ($achievements as $achievement)
                <div wire:key="achievement-card-{{ $achievement->id }}" class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-words font-semibold leading-snug text-ink">{{ $achievement->judul }}</p>
                            <p class="mt-1 break-words text-xs text-ink-soft">
                                {{ $achievement->penyelenggara ?: 'Penyelenggara belum diisi' }}
                            </p>
                        </div>
                        <span class="inline-flex shrink-0 items-center rounded-full border border-gold/30 bg-gold/15 px-2.5 py-1 text-xs font-semibold text-gold-text">
                            {{ $achievement->peringkat }}
                        </span>
                    </div>

                    <div class="mt-2 flex flex-wrap gap-1.5">
                        <span class="inline-flex rounded-full border border-line bg-paper px-2 py-0.5 text-[11px] font-medium text-ink-soft">
                            {{ $achievement->labelTingkat() }}
                        </span>
                        <span class="inline-flex rounded-full border border-line bg-paper px-2 py-0.5 text-[11px] font-medium text-ink-soft">
                            {{ $achievement->labelBidang() }}
                        </span>
                        <span class="inline-flex rounded-full border border-line bg-paper px-2 py-0.5 text-[11px] font-medium text-ink-soft">
                            {{ $achievement->tahun }}
                        </span>
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-2 border-t border-line pt-3">
                        <button type="button" wire:click="edit({{ $achievement->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-bone px-3 py-2 text-xs font-semibold text-teal-bright transition hover:bg-mint">
                            <x-admin.icon name="pencil" class="h-4 w-4" />
                            Edit
                        </button>
                        <button type="button" wire:click="confirmDelete({{ $achievement->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-danger/30 px-3 py-2 text-xs font-semibold text-danger-text transition hover:bg-danger/10">
                            <x-admin.icon name="trash" class="h-4 w-4" />
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-ink-soft">
                    Belum ada data prestasi. Klik <strong>Tambah Prestasi</strong> untuk menambahkan data pertama.
                </div>
            @endforelse
        </div>

        @if ($achievements->hasPages())
            <div class="border-t border-line px-5 py-4">
                {{ $achievements->links() }}
            </div>
        @endif
    </div>

    {{-- Modal form tambah/edit --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeForm"
             role="dialog" aria-modal="true" aria-labelledby="achievement-form-title">
            <div class="mx-auto my-8 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <h3 id="achievement-form-title" class="font-display text-lg font-semibold">
                        {{ $editingId ? 'Edit Prestasi' : 'Tambah Prestasi' }}
                    </h3>
                    <button type="button" wire:click="closeForm"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4 p-5">
                    <div>
                        <label for="judul" class="block text-sm font-medium text-ink-soft">
                            Judul <span class="text-danger-text">*</span>
                        </label>
                        <input id="judul" type="text" wire:model="judul"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Contoh: Olimpiade Sains Nasional Matematika">
                        @error('judul')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="peringkat" class="block text-sm font-medium text-ink-soft">
                            Peringkat <span class="text-danger-text">*</span>
                        </label>
                        <input id="peringkat" type="text" wire:model="peringkat"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Contoh: Juara 1">
                        @error('peringkat')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tingkat" class="block text-sm font-medium text-ink-soft">
                            Tingkat <span class="text-danger-text">*</span>
                        </label>
                        <select id="tingkat" wire:model="tingkat"
                                class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                            <option value="">Pilih tingkat</option>
                            @foreach ($tingkatOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('tingkat')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bidang" class="block text-sm font-medium text-ink-soft">
                            Bidang <span class="text-danger-text">*</span>
                        </label>
                        <select id="bidang" wire:model="bidang"
                                class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                            <option value="">Pilih bidang</option>
                            @foreach ($bidangOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('bidang')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tahun" class="block text-sm font-medium text-ink-soft">
                            Tahun <span class="text-danger-text">*</span>
                        </label>
                        <input id="tahun" type="number" wire:model="tahun" min="1900" max="{{ now()->year + 1 }}"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                        @error('tahun')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="penyelenggara" class="block text-sm font-medium text-ink-soft">
                            Penyelenggara
                        </label>
                        <input id="penyelenggara" type="text" wire:model="penyelenggara"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Contoh: Dinas Pendidikan Kabupaten">
                        @error('penyelenggara')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeForm"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                                class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal konfirmasi hapus --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:click.self="cancelDelete"
             role="dialog" aria-modal="true" aria-labelledby="achievement-delete-title">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="p-5">
                    <h3 id="achievement-delete-title" class="font-display text-lg font-semibold">
                        Hapus "{{ $deletingJudul }}"?
                    </h3>
                    <p class="mt-2 text-sm text-ink-soft">
                        Tindakan ini tidak dapat dibatalkan. Data prestasi akan dihapus permanen dari database.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-2">
                        <button type="button" wire:click="cancelDelete"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete"
                                class="inline-flex items-center justify-center rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-danger/90 disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                            <span wire:loading wire:target="delete">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
