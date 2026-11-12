<div class="mx-auto max-w-6xl space-y-6">

    {{-- Judul halaman --}}
    <div>
        <h2 class="font-display text-xl font-semibold">Statistik Madrasah</h2>
        <p class="mt-1 text-sm text-ink-soft">
            Jenis statistik sudah ditetapkan (Siswa, Guru, Tenaga Kependidikan, Kelas). Klik <strong>Ubah Angka</strong>
            pada kartu untuk memperbarui jumlahnya — perubahan langsung tampil di bagian "Madrasah dalam angka" pada beranda.
        </p>
    </div>

    {{-- Kartu statistik --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($statistics as $statistic)
            <div wire:key="statistic-{{ $statistic->id }}"
                 class="flex flex-col justify-between rounded-xl border border-line bg-bone p-5 transition hover:border-teal-bright">
                <div class="flex items-center gap-3">
                    <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-brand text-on-brand shadow-sm\">
                        <x-public.icon :name="$statistic->icon" :size="24" :strokeWidth="2" />
                    </span>
                    <p class="text-sm font-medium text-ink-soft">{{ $statistic->label }}</p>
                </div>

                <p class="mt-4 font-display text-3xl font-semibold text-teal-deep">
                    {{ number_format($statistic->value, 0, ',', '.') }}
                </p>

                <button type="button" wire:click="edit({{ $statistic->id }})"
                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-paper px-3 py-2 text-sm font-semibold text-teal-bright transition hover:border-teal-bright hover:bg-mint">
                    <x-admin.icon name="pencil" class="h-4 w-4" />
                    Ubah Angka
                </button>
            </div>
        @endforeach
    </div>

    {{-- Modal ubah angka --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:click.self="closeModal"
             role="dialog" aria-modal="true" aria-labelledby="statistic-form-title">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <h3 id="statistic-form-title" class="font-display text-lg font-semibold">
                        Ubah Angka &middot; {{ $editingLabel }}
                    </h3>
                    <button type="button" wire:click="closeModal"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4 p-5">
                    <div>
                        <label class="block text-sm font-medium text-ink-soft">Jenis Statistik</label>
                        <p class="mt-1 rounded-lg border border-line bg-paper px-3 py-2.5 text-sm text-ink">
                            {{ $editingLabel }}
                        </p>
                        <p class="mt-1 text-xs text-ink-soft">Jenis statistik sudah ditetapkan dan tidak bisa diubah di sini.</p>
                    </div>

                    <div>
                        <label for="value" class="block text-sm font-medium text-ink-soft">
                            Angka <span class="text-danger-text">*</span>
                        </label>
                        <input id="value" type="number" min="0" max="1000000" wire:model="value" autofocus
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Contoh: 595">
                        @error('value')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-ink-soft">
                            Ikon <span class="text-danger-text">*</span>
                        </label>
                        <select id="icon" wire:model="icon"
                                class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                            @foreach ($iconOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('icon')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeModal"
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
</div>
