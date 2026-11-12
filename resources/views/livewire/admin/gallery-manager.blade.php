<div class="mx-auto max-w-6xl space-y-6">

    @if ($selectedGallery)
        {{-- Kelola foto satu album --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ route('admin.galleries.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-teal-bright hover:underline">
                    <x-admin.icon name="chevron-left" class="h-4 w-4" />
                    Kembali ke daftar album
                </a>

                <h2 class="mt-3 font-display text-xl font-semibold">{{ $selectedGallery->album }}</h2>
                <p class="mt-1 max-w-2xl text-sm text-ink-soft">
                    {{ $selectedGallery->keterangan ?: 'Belum ada keterangan album.' }}
                </p>
                <p class="mt-2 text-xs text-ink-soft">
                    {{ $selectedGallery->images_count }} foto
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="editAlbum({{ $selectedGallery->id }})"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                    <x-admin.icon name="pencil" class="h-4 w-4" />
                    Ubah Album
                </button>
                <button type="button" wire:click="confirmDeleteAlbum({{ $selectedGallery->id }})"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-danger/90">
                    <x-admin.icon name="trash" class="h-4 w-4" />
                    Hapus Album
                </button>
            </div>
        </div>

        {{-- Upload banyak foto --}}
        <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
            <div class="border-b border-line px-5 py-4">
                <h3 class="font-display text-base font-semibold">Unggah Foto</h3>
                <p class="mt-1 text-sm text-ink-soft">
                    Pilih beberapa foto sekaligus, lalu klik Unggah. Setiap foto divalidasi satu per satu.
                </p>
            </div>

            <div class="p-5"
                 x-data="{ uploading: false, progress: 0, dragging: false }"
                 x-on:livewire-upload-start="uploading = true; progress = 0"
                 x-on:livewire-upload-finish="uploading = false; progress = 100"
                 x-on:livewire-upload-cancel="uploading = false"
                 x-on:livewire-upload-error="uploading = false"
                 x-on:livewire-upload-progress="progress = $event.detail.progress"
                 x-on:photos-reset.window="$refs.photoInput.value = ''">

                <input id="gallery-photos" x-ref="photoInput" type="file" wire:model="photos"
                       multiple accept="image/jpeg" class="sr-only">

                <label for="gallery-photos"
                       @dragover.prevent="dragging = true"
                       @dragleave.prevent="dragging = false"
                       @drop.prevent="dragging = false; $refs.photoInput.files = $event.dataTransfer.files; $refs.photoInput.dispatchEvent(new Event('change', { bubbles: true }))"
                       :class="dragging ? 'border-teal-bright bg-mint' : 'border-line bg-bone'"
                       class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-5 py-8 text-center transition">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand text-on-brand\">
                        <x-admin.icon name="upload" class="h-6 w-6" />
                    </span>
                    <span class="mt-3 text-sm font-semibold text-ink">
                        Tarik foto ke sini atau klik untuk pilih file
                    </span>
                    <span class="mt-1 text-xs text-ink-soft">
                        Hanya JPG · maksimal {{ config('cms.image.max_kb') / 1024 }} MB per foto · bisa pilih beberapa sekaligus
                    </span>
                </label>

                <div x-show="uploading" x-cloak class="mt-4">
                    <div class="h-2 overflow-hidden rounded-full bg-paper">
                        <div class="h-2 rounded-full bg-brand transition-all duration-200"
                             :style="'width: ' + progress + '%'"></div>
                    </div>
                    <p class="mt-2 text-xs text-ink-soft">
                        Mengunggah file... <span class="font-semibold text-teal-bright" x-text="progress + '%'"></span>
                    </p>
                </div>

                @if ($photos !== [])
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($photos as $index => $photo)
                            @php
                                $extension = strtolower($photo->getClientOriginalExtension());
                                $previewable = in_array($extension, config('cms.image.extensions'), true);
                            @endphp

                            <div wire:key="pending-photo-{{ $index }}-{{ $photo->getClientOriginalName() }}"
                                 class="relative overflow-hidden rounded-xl border bg-paper {{ $errors->has("photos.{$index}") ? 'border-danger' : 'border-line' }}">
                                @if ($previewable)
                                    <img src="{{ $photo->temporaryUrl() }}"
                                         alt="Preview {{ $photo->getClientOriginalName() }}"
                                         class="aspect-[4/3] w-full object-cover">
                                @else
                                    <div class="flex aspect-[4/3] items-center justify-center bg-paper text-xs text-ink-soft">
                                        Preview tidak tersedia
                                    </div>
                                @endif

                                <button type="button" wire:click="removePendingPhoto({{ $index }})"
                                        title="Hapus {{ $photo->getClientOriginalName() }} dari antrean"
                                        class="absolute right-2 top-2 rounded-full bg-ink/70 p-1.5 text-white transition hover:bg-danger">
                                    <x-admin.icon name="x" class="h-4 w-4" />
                                    <span class="sr-only">Hapus dari antrean</span>
                                </button>

                                <div class="p-2">
                                    <p class="truncate text-xs font-medium text-ink" title="{{ $photo->getClientOriginalName() }}">
                                        {{ $photo->getClientOriginalName() }}
                                    </p>
                                    <p class="text-[11px] text-ink-soft">
                                        {{ number_format($photo->getSize() / 1024 / 1024, 1, ',', '.') }} MB
                                    </p>
                                    @error("photos.{$index}")
                                        <p class="mt-1 text-[11px] leading-snug text-danger-text">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="uploadPhotos"
                                wire:loading.attr="disabled" wire:target="uploadPhotos"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                            <x-admin.icon name="upload" class="h-4 w-4" />
                            <span wire:loading.remove wire:target="uploadPhotos">
                                Unggah {{ count($photos) }} Foto
                            </span>
                            <span wire:loading wire:target="uploadPhotos">Menyimpan...</span>
                        </button>

                        <button type="button" wire:click="clearPendingPhotos"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Bersihkan Pilihan
                        </button>
                    </div>
                @endif

                @error('photos')
                    <p class="mt-3 text-xs text-danger-text">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Grid foto album --}}
        <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                <div>
                    <h3 class="font-display text-base font-semibold">Foto Album</h3>
                    <p class="mt-1 text-sm text-ink-soft">
                        Gunakan tombol panah untuk mengatur urutan tampil foto.
                    </p>
                </div>
                <span class="rounded-full bg-brand px-3 py-1 text-xs font-semibold text-on-brand">
                    {{ $selectedGallery->images_count }} Foto
                </span>
            </div>

            @if ($selectedGallery->images->isEmpty())
                <div class="px-5 py-12 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gold/15 text-gold-text">
                        <x-admin.icon name="photo" class="h-6 w-6" />
                    </span>
                    <p class="mt-3 font-display text-base font-semibold">Belum ada foto</p>
                    <p class="mt-1 text-sm text-ink-soft">Unggah foto pertama menggunakan panel di atas.</p>
                </div>
            @else
                <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($selectedGallery->images as $image)
                        <div wire:key="gallery-photo-{{ $image->id }}"
                             class="overflow-hidden rounded-xl border border-line bg-paper">
                            <div class="relative aspect-[4/3] bg-teal-950">
                                @if ($image->hasMedia('foto'))
                                    <img src="{{ $image->getFirstMediaUrl('foto') }}"
                                         alt="Foto {{ $loop->iteration }} - {{ $selectedGallery->album }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-xs text-white/80">
                                        Foto tidak tersedia
                                    </div>
                                @endif

                                <span class="absolute left-2 top-2 rounded-full bg-teal-950/80 px-2 py-0.5 text-[10px] font-bold text-white backdrop-blur">
                                    #{{ $image->urutan }}
                                </span>

                                <button type="button" wire:click="confirmDeletePhoto({{ $image->id }})"
                                        title="Hapus foto {{ $loop->iteration }}"
                                        class="absolute right-2 top-2 rounded-full bg-danger p-1.5 text-white transition hover:bg-danger/90">
                                    <x-admin.icon name="trash" class="h-4 w-4" />
                                    <span class="sr-only">Hapus foto</span>
                                </button>
                            </div>

                            <div class="flex items-center justify-between gap-2 p-2">
                                <span class="text-xs font-medium text-ink-soft">Foto {{ $loop->iteration }}</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" wire:click="movePhotoUp({{ $image->id }})"
                                            @disabled($loop->first)
                                            title="Naikkan foto"
                                            class="rounded-lg p-1.5 text-teal-bright transition hover:bg-mint disabled:cursor-not-allowed disabled:opacity-30">
                                        <x-admin.icon name="chevron-up" class="h-4 w-4" />
                                        <span class="sr-only">Naikkan foto</span>
                                    </button>
                                    <button type="button" wire:click="movePhotoDown({{ $image->id }})"
                                            @disabled($loop->last)
                                            title="Turunkan foto"
                                            class="rounded-lg p-1.5 text-teal-bright transition hover:bg-mint disabled:cursor-not-allowed disabled:opacity-30">
                                        <x-admin.icon name="chevron-down" class="h-4 w-4" />
                                        <span class="sr-only">Turunkan foto</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        {{-- Daftar album --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-display text-xl font-semibold">Daftar Album Galeri</h2>
                <p class="mt-1 text-sm text-ink-soft">
                    Kelola album dan foto dokumentasi kegiatan madrasah. Album terbaru otomatis tampil paling atas di halaman publik.
                </p>
            </div>

            <button type="button" wire:click="createAlbum"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover">
                <x-admin.icon name="plus" class="h-4 w-4" />
                Tambah Album
            </button>
        </div>

        <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full text-sm">
                    <thead class="bg-paper text-left text-xs uppercase tracking-wide text-ink-soft">
                        <tr>
                            <th class="px-5 py-3 font-medium">Cover</th>
                            <th class="px-5 py-3 font-medium">Album</th>
                            <th class="px-5 py-3 font-medium">Keterangan</th>
                            <th class="px-5 py-3 font-medium">Foto</th>
                            <th class="px-5 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($galleries as $gallery)
                            @php($cover = $gallery->images->first())
                            <tr wire:key="gallery-{{ $gallery->id }}" class="odd:bg-bone even:bg-paper/40">
                                <td class="px-5 py-3">
                                    <div class="h-14 w-20 overflow-hidden rounded-lg bg-teal-950">
                                        @if ($cover?->hasMedia('foto'))
                                            <img src="{{ $cover->getFirstMediaUrl('foto') }}"
                                                 alt="Cover {{ $gallery->album }}"
                                                 loading="lazy"
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-white/70">
                                                <x-admin.icon name="photo" class="h-5 w-5" />
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-ink">{{ $gallery->album }}</p>
                                    <p class="mt-0.5 text-xs text-ink-soft">
                                        Dibuat {{ $gallery->created_at->locale('id')->translatedFormat('d M Y') }}
                                    </p>
                                </td>
                                <td class="max-w-xs px-5 py-3 text-ink-soft">
                                    <p class="line-clamp-2">{{ $gallery->keterangan ?: '-' }}</p>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center rounded-full bg-mint px-2.5 py-1 text-xs font-semibold text-teal-bright">
                                        {{ $gallery->images_count }} Foto
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.galleries.show', $gallery) }}"
                                           title="Kelola foto {{ $gallery->album }}"
                                           class="rounded-lg p-2 text-teal-bright transition hover:bg-mint">
                                            <x-admin.icon name="photo" class="h-4 w-4" />
                                            <span class="sr-only">Kelola foto</span>
                                        </a>

                                        <button type="button" wire:click="editAlbum({{ $gallery->id }})"
                                                title="Edit {{ $gallery->album }}"
                                                class="rounded-lg p-2 text-teal-bright transition hover:bg-mint">
                                            <x-admin.icon name="pencil" class="h-4 w-4" />
                                            <span class="sr-only">Edit album</span>
                                        </button>

                                        <button type="button" wire:click="confirmDeleteAlbum({{ $gallery->id }})"
                                                title="Hapus {{ $gallery->album }}"
                                                class="rounded-lg p-2 text-danger-text transition hover:bg-danger/10">
                                            <x-admin.icon name="trash" class="h-4 w-4" />
                                            <span class="sr-only">Hapus album</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-ink-soft">
                                    Belum ada album galeri. Klik <strong>Tambah Album</strong> untuk membuat album pertama.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Daftar kartu untuk layar HP --}}
            <div class="divide-y divide-line md:hidden">
                @forelse ($galleries as $gallery)
                    @php($cover = $gallery->images->first())
                    <div wire:key="gallery-card-{{ $gallery->id }}" class="p-4">
                        <div class="flex items-start gap-3">
                            <div class="h-16 w-20 shrink-0 overflow-hidden rounded-lg bg-teal-950">
                                @if ($cover?->hasMedia('foto'))
                                    <img src="{{ $cover->getFirstMediaUrl('foto') }}"
                                         alt="Cover {{ $gallery->album }}"
                                         loading="lazy"
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-white/70">
                                        <x-admin.icon name="photo" class="h-5 w-5" />
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="font-semibold leading-snug text-ink">{{ $gallery->album }}</p>
                                <p class="mt-0.5 text-xs text-ink-soft">
                                    {{ $gallery->created_at->locale('id')->translatedFormat('d M Y') }}
                                </p>
                                <p class="mt-1 line-clamp-2 text-xs text-ink-soft">{{ $gallery->keterangan ?: 'Tanpa keterangan' }}</p>
                                <span class="mt-2 inline-flex items-center rounded-full bg-mint px-2 py-0.5 text-[11px] font-semibold text-teal-bright">
                                    {{ $gallery->images_count }} Foto
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center justify-end gap-2 border-t border-line pt-3">
                            <a href="{{ route('admin.galleries.show', $gallery) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-bone px-3 py-2 text-xs font-semibold text-teal-bright transition hover:bg-mint">
                                <x-admin.icon name="photo" class="h-4 w-4" />
                                Kelola Foto
                            </a>
                            <button type="button" wire:click="editAlbum({{ $gallery->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-bone px-3 py-2 text-xs font-semibold text-ink transition hover:bg-paper">
                                <x-admin.icon name="pencil" class="h-4 w-4" />
                                Edit
                            </button>
                            <button type="button" wire:click="confirmDeleteAlbum({{ $gallery->id }})"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-danger/30 px-3 py-2 text-xs font-semibold text-danger-text transition hover:bg-danger/10">
                                <x-admin.icon name="trash" class="h-4 w-4" />
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-sm text-ink-soft">
                        Belum ada album galeri. Klik <strong>Tambah Album</strong> untuk membuat album pertama.
                    </div>
                @endforelse
            </div>

            @if ($galleries->hasPages())
                <div class="border-t border-line px-5 py-4">
                    {{ $galleries->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- Modal form album --}}
    @if ($showAlbumForm)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeAlbumForm"
             role="dialog" aria-modal="true" aria-labelledby="gallery-album-title">
            <div class="mx-auto my-8 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <h3 id="gallery-album-title" class="font-display text-lg font-semibold">
                        {{ $editingAlbumId ? 'Edit Album' : 'Tambah Album' }}
                    </h3>
                    <button type="button" wire:click="closeAlbumForm"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="saveAlbum" class="space-y-4 p-5">
                    <div>
                        <label for="album" class="block text-sm font-medium text-ink-soft">
                            Nama Album <span class="text-danger-text">*</span>
                        </label>
                        <input id="album" type="text" wire:model="album"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Contoh: Upacara Hari Senin">
                        @error('album')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-ink-soft">
                            Keterangan
                        </label>
                        <textarea id="keterangan" wire:model="keterangan" rows="4"
                                  class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                                  placeholder="Deskripsi singkat album, misalnya kegiatan dan lokasi (opsional)."></textarea>
                        @error('keterangan')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeAlbumForm"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveAlbum"
                                class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveAlbum">Simpan Album</span>
                            <span wire:loading wire:target="saveAlbum">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal konfirmasi hapus album --}}
    @if ($showDeleteAlbumModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:click.self="cancelDeleteAlbum"
             role="dialog" aria-modal="true" aria-labelledby="gallery-delete-title">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger-text">
                            <x-admin.icon name="trash" class="h-5 w-5" />
                        </span>
                        <div>
                            <h3 id="gallery-delete-title" class="font-display text-lg font-semibold">
                                Hapus album "{{ $deletingAlbumName }}"?
                            </h3>
                            <p class="mt-2 text-sm text-ink-soft">
                                @if ($deletingAlbumPhotoCount > 0)
                                    Semua <strong>{{ $deletingAlbumPhotoCount }} foto</strong> di dalam album ini akan dihapus permanen dari penyimpanan, bukan hanya disembunyikan.
                                @else
                                    Album ini akan dihapus permanen dari database.
                                @endif
                                Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-2">
                        <button type="button" wire:click="cancelDeleteAlbum"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteAlbum" wire:loading.attr="disabled" wire:target="deleteAlbum"
                                class="inline-flex items-center justify-center rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-danger/90 disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="deleteAlbum">Ya, Hapus Album</span>
                            <span wire:loading wire:target="deleteAlbum">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal konfirmasi hapus foto --}}
    @if ($showDeletePhotoModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:click.self="cancelDeletePhoto"
             role="dialog" aria-modal="true" aria-labelledby="gallery-photo-delete-title">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="p-5">
                    <h3 id="gallery-photo-delete-title" class="font-display text-lg font-semibold">
                        Hapus foto ini?
                    </h3>

                    @if ($deletingPhotoUrl !== '')
                        <img src="{{ $deletingPhotoUrl }}"
                             alt="Foto yang akan dihapus"
                             class="mt-4 aspect-[4/3] w-full rounded-lg object-cover">
                    @endif

                    <p class="mt-4 text-sm text-ink-soft">
                        Foto akan dihapus permanen dari album
                        <strong>{{ $deletingPhotoAlbumName }}</strong> dan dari penyimpanan.
                        Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-2">
                        <button type="button" wire:click="cancelDeletePhoto"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="button" wire:click="deletePhoto" wire:loading.attr="disabled" wire:target="deletePhoto"
                                class="inline-flex items-center justify-center rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-danger/90 disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="deletePhoto">Ya, Hapus Foto</span>
                            <span wire:loading wire:target="deletePhoto">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
