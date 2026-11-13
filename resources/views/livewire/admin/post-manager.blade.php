@php($typeLabel = $pageType === 'artikel' ? 'Artikel' : 'Berita')

<div class="mx-auto max-w-6xl space-y-6">

    {{-- Judul halaman + tombol tambah --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-xl font-semibold">Daftar {{ $typeLabel }}</h2>
            <p class="mt-1 text-sm text-ink-soft">
                Kelola {{ strtolower($typeLabel) }} madrasah. Post berstatus Publish langsung tampil di halaman publik.
            </p>
        </div>

        <button type="button" wire:click="create"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover">
            <x-admin.icon name="plus" class="h-4 w-4" />
            Buat {{ $typeLabel }}
        </button>
    </div>

    {{-- Tabel + filter --}}
    <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
        <div class="flex flex-col gap-3 border-b border-line p-4 lg:flex-row lg:flex-wrap lg:items-center">
            <div class="relative w-full lg:max-w-xs">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-ink-soft">
                    <x-admin.icon name="search" class="h-4 w-4" />
                </span>
                <input type="search" wire:model.live.debounce.300ms="search"
                       placeholder="Cari judul..."
                       class="w-full rounded-lg border-line bg-bone pl-9 text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
            </div>

            <select wire:model.live="filterStatus"
                    class="w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright lg:w-auto">
                <option value="">Semua Status</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            @if ($filterStatus || $search)
                <button type="button" wire:click="resetFilters"
                        class="text-sm font-medium text-teal-bright hover:underline">
                    Reset filter
                </button>
            @endif

            <span wire:loading wire:target="search,filterStatus"
                  class="text-xs text-ink-soft">
                Memuat...
            </span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-sm">
                <thead class="bg-paper text-left text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-5 py-3 font-medium">Judul</th>
                        <th class="px-5 py-3 font-medium">Tipe</th>
                        <th class="px-5 py-3 font-medium">Kategori</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($posts as $post)
                        <tr wire:key="post-{{ $post->id }}" class="odd:bg-bone even:bg-paper/40">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($post->hasMedia('sampul'))
                                        <img src="{{ $post->getFirstMediaUrl('sampul') }}"
                                             alt="Sampul {{ $post->judul }}"
                                             loading="lazy"
                                             class="h-11 w-16 shrink-0 rounded-md object-cover">
                                    @else
                                        <span class="flex h-11 w-16 shrink-0 items-center justify-center rounded-md bg-teal-950 text-gold">
                                            <x-admin.icon name="photo" class="h-4 w-4" />
                                        </span>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="flex items-center gap-1.5 font-medium text-ink">
                                            <span class="truncate">{{ $post->judul }}</span>
                                            @if ($post->sematkan)
                                                <x-admin.icon name="star" class="h-3.5 w-3.5 shrink-0 text-gold-text" title="Disematkan di beranda" />
                                            @endif
                                        </p>
                                        <p class="truncate text-xs text-ink-soft">/{{ $post->type }}/{{ $post->slug }}</p>
                                        <p class="mt-0.5 truncate text-xs text-ink-soft">Penulis: {{ $post->namaPenulis() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $post->type === 'berita' ? 'bg-mint text-teal-bright' : 'bg-gold/15 text-gold-text' }}">
                                    {{ $post->labelType() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-ink-soft">{{ $post->labelKategori() }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $post->status === 'publish' ? 'bg-success/10 text-success-text' : 'border border-line bg-paper text-ink-soft' }}">
                                    {{ $post->labelStatus() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-ink-soft">{{ $post->tanggalPublishLabel() }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" wire:click="edit({{ $post->id }})"
                                            title="Edit {{ $post->judul }}"
                                            class="rounded-lg p-2 text-teal-bright transition hover:bg-mint">
                                        <x-admin.icon name="pencil" class="h-4 w-4" />
                                        <span class="sr-only">Edit</span>
                                    </button>

                                    <button type="button" wire:click="confirmDelete({{ $post->id }})"
                                            title="Hapus {{ $post->judul }}"
                                            class="rounded-lg p-2 text-danger-text transition hover:bg-danger/10">
                                        <x-admin.icon name="trash" class="h-4 w-4" />
                                        <span class="sr-only">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-ink-soft">
                                Belum ada {{ strtolower($typeLabel) }}. Klik <strong>Buat {{ $typeLabel }}</strong> untuk menambahkan yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Daftar kartu untuk layar HP --}}
        <div class="divide-y divide-line md:hidden">
            @forelse ($posts as $post)
                <div wire:key="post-card-{{ $post->id }}" class="p-4">
                    <div class="flex items-start gap-3">
                        @if ($post->hasMedia('sampul'))
                            <img src="{{ $post->getFirstMediaUrl('sampul') }}"
                                 alt="Sampul {{ $post->judul }}"
                                 loading="lazy"
                                 class="h-16 w-20 shrink-0 rounded-md object-cover">
                        @else
                            <span class="flex h-16 w-20 shrink-0 items-center justify-center rounded-md bg-teal-950 text-gold">
                                <x-admin.icon name="photo" class="h-5 w-5" />
                            </span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="flex items-start gap-1.5 font-semibold leading-snug text-ink">
                                <span class="line-clamp-2 break-words">{{ $post->judul }}</span>
                                @if ($post->sematkan)
                                    <x-admin.icon name="star" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold-text" title="Disematkan di beranda" />
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-ink-soft">
                                {{ $post->tanggalPublishLabel() }} · {{ $post->namaPenulis() }}
                            </p>
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $post->type === 'berita' ? 'bg-mint text-teal-bright' : 'bg-gold/15 text-gold-text' }}">
                                    {{ $post->labelType() }}
                                </span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $post->status === 'publish' ? 'bg-success/10 text-success-text' : 'border border-line bg-paper text-ink-soft' }}">
                                    {{ $post->labelStatus() }}
                                </span>
                                @if ($post->type === 'berita')
                                    <span class="inline-flex rounded-full border border-line bg-paper px-2 py-0.5 text-[11px] font-medium text-ink-soft">
                                        {{ $post->labelKategori() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-end gap-2 border-t border-line pt-3">
                        <button type="button" wire:click="edit({{ $post->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-bone px-3 py-2 text-xs font-semibold text-teal-bright transition hover:bg-mint">
                            <x-admin.icon name="pencil" class="h-4 w-4" />
                            Edit
                        </button>
                        <button type="button" wire:click="confirmDelete({{ $post->id }})"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-danger/30 px-3 py-2 text-xs font-semibold text-danger-text transition hover:bg-danger/10">
                            <x-admin.icon name="trash" class="h-4 w-4" />
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-ink-soft">
                    Belum ada {{ strtolower($typeLabel) }}. Klik <strong>Buat {{ $typeLabel }}</strong> untuk menambahkan yang pertama.
                </div>
            @endforelse
        </div>

        @if ($posts->hasPages())
            <div class="border-t border-line px-5 py-4">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    {{-- Modal form tambah/edit --}}
    @if ($showForm)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeForm"
             role="dialog" aria-modal="true" aria-labelledby="post-form-title">
            <div class="mx-auto my-4 w-full max-w-6xl overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <div>
                        <h3 id="post-form-title" class="font-display text-lg font-semibold">
                            {{ $editingId ? 'Edit Post' : 'Buat Post Baru' }}
                        </h3>
                        <p class="mt-0.5 text-xs text-ink-soft">
                            Slug dibuat otomatis dari judul, tetapi tetap bisa diubah manual.
                        </p>
                    </div>
                    <button type="button" wire:click="closeForm"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="save" class="p-5">
                    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">

                        {{-- Kolom kiri: informasi utama + editor --}}
                        <div class="space-y-5">
                            <section class="rounded-xl border border-line bg-paper p-4">
                                <h4 class="font-display text-sm font-semibold uppercase tracking-wide text-ink-soft">
                                    Informasi Utama
                                </h4>

                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="judul" class="block text-sm font-medium text-ink-soft">
                                            Judul <span class="text-danger-text">*</span>
                                        </label>
                                        <input id="judul" type="text" wire:model.blur="judul"
                                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                                               placeholder="Contoh: Kegiatan Muhadharah Siswa Kelas IX">
                                        @error('judul')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="slug" class="block text-sm font-medium text-ink-soft">
                                            Alamat halaman
                                        </label>
                                        <div class="mt-1 flex overflow-hidden rounded-lg border border-line bg-bone shadow-sm focus-within:border-teal-bright focus-within:ring-1 focus-within:ring-teal-bright">
                                            <span class="inline-flex items-center border-r border-line px-3 text-xs text-ink-soft">
                                                /{{ $type === 'artikel' ? 'artikel' : 'berita' }}/
                                            </span>
                                            <input id="slug" type="text" wire:model.blur="slug"
                                                   class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm text-ink placeholder:text-ink-soft/60 focus:ring-0"
                                                   placeholder="alamat-halaman">
                                            <button type="button" wire:click="generateSlugFromJudul"
                                                    class="shrink-0 border-l border-line px-3 text-xs font-semibold text-teal-bright transition hover:bg-mint">
                                                Otomatis
                                            </button>
                                        </div>
                                        <p class="mt-1 text-xs text-ink-soft">
                                            Jika bentrok, sistem menambahkan akhiran angka secara otomatis.
                                        </p>
                                        @error('slug')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="ringkasan" class="block text-sm font-medium text-ink-soft">
                                            Ringkasan
                                        </label>
                                        <textarea id="ringkasan" rows="3" wire:model="ringkasan"
                                                  class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                                                  placeholder="Ringkasan singkat untuk kartu di halaman publik (opsional)."></textarea>
                                        @error('ringkasan')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-ink-soft">
                                            Isi Artikel <span class="text-danger-text">*</span>
                                        </label>

                                        <div wire:ignore
                                             x-data="tiptapEditor(@js($isi), @js(route('admin.posts.inline-image')))"
                                             :class="fullscreen ? 'fixed inset-0 z-[70] m-0 rounded-none border-0' : 'mt-1 rounded-lg border border-line'"
                                             class="flex flex-col overflow-hidden bg-bone shadow-sm">
                                            @include('livewire.admin.partials.tiptap-toolbar')

                                            <div class="flex-1 overflow-y-auto bg-bone" :class="fullscreen ? 'max-h-none' : 'max-h-[60vh]'">
                                                <div x-ref="editor"
                                                     @click="focusEditor($event)"
                                                     class="tiptap-content min-h-[22rem] cursor-text p-4"
                                                     :class="fullscreen && 'min-h-full'"></div>
                                            </div>

                                            <div class="flex shrink-0 flex-wrap items-center justify-between gap-2 border-t border-line bg-paper px-3 py-1.5 text-[11px] text-ink-soft">
                                                <span>
                                                    <span x-text="stats.words">0</span> kata
                                                    <span class="mx-1 text-line">·</span>
                                                    <span x-text="stats.characters">0</span> karakter
                                                </span>
                                                <span x-show="uploading" x-cloak>Mengunggah gambar...</span>
                                                <button type="button" @click="toggleFullscreen()"
                                                        :aria-pressed="fullscreen.toString()"
                                                        class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 font-semibold text-teal-bright transition hover:bg-mint">
                                                    <span x-show="! fullscreen">Layar penuh</span>
                                                    <span x-show="fullscreen" x-cloak>Keluar layar penuh</span>
                                                </button>
                                            </div>

                                            <input x-ref="imageInput" type="file"
                                                   accept="image/jpeg"
                                                   class="hidden"
                                                   @change="uploadImage($event)">
                                        </div>

                                        @error('isi')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </section>
                        </div>

                        {{-- Kolom kanan: metadata + sampul --}}
                        <aside class="space-y-5">
                            <section class="rounded-xl border border-line bg-paper p-4">
                                <h4 class="font-display text-sm font-semibold uppercase tracking-wide text-ink-soft">
                                    Pengaturan Tayang
                                </h4>

                                <div class="mt-4 space-y-4">
                                    <div>
                                        <p class="block text-sm font-medium text-ink-soft">Jenis</p>
                                        <p class="mt-1 rounded-lg border border-line bg-bone px-3 py-2 text-sm font-medium text-ink">
                                            {{ $typeLabel }}
                                        </p>
                                        <p class="mt-1 text-xs text-ink-soft">
                                            Jenis mengikuti halaman admin yang sedang dibuka.
                                        </p>
                                    </div>

                                    @if ($type === 'berita')
                                        <div>
                                            <label for="kategori" class="block text-sm font-medium text-ink-soft">
                                                Kategori <span class="text-danger-text">*</span>
                                            </label>
                                            <select id="kategori" wire:model="kategori"
                                                    class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                                                <option value="">Pilih kategori</option>
                                                @foreach ($kategoriOptions as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('kategori')
                                                <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif

                                    <div>
                                        <label for="status" class="block text-sm font-medium text-ink-soft">
                                            Status <span class="text-danger-text">*</span>
                                        </label>
                                        <select id="status" wire:model.live="status"
                                                class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <p class="mt-1 text-xs text-ink-soft">
                                            {{ $status === 'publish' ? 'Post langsung tampil di halaman publik.' : 'Draft tidak tampil di halaman publik.' }}
                                        </p>
                                        @error('status')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="penulis_nama" class="block text-sm font-medium text-ink-soft">
                                            Nama Penulis <span class="text-danger-text">*</span>
                                        </label>
                                        <input id="penulis_nama" type="text" wire:model.blur="penulis_nama"
                                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                                               placeholder="Contoh: Giyono, S.Pd.">
                                        <p class="mt-1 text-xs text-ink-soft">
                                            Nama yang tampil sebagai penulis di halaman publik.
                                        </p>
                                        @error('penulis_nama')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="tanggal_publish" class="block text-sm font-medium text-ink-soft">
                                            Tanggal Tayang <span class="text-danger-text">*</span>
                                        </label>
                                        <input id="tanggal_publish" type="date" wire:model="tanggal_publish"
                                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright">
                                        @error('tanggal_publish')
                                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <label class="flex items-start justify-between gap-3 rounded-lg border border-line bg-bone p-3">
                                        <span>
                                            <span class="block text-sm font-medium text-ink">Sematkan di beranda</span>
                                            <span class="mt-0.5 block text-xs text-ink-soft">Tampil di posisi atas listing publik.</span>
                                        </span>
                                        <span class="relative mt-0.5 inline-flex shrink-0 items-center">
                                            <input type="checkbox" wire:model="sematkan" class="peer sr-only">
                                            <span class="h-6 w-11 rounded-full bg-line transition peer-checked:bg-brand"></span>
                                            <span class="absolute left-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                                        </span>
                                    </label>
                                </div>
                            </section>

                            <section class="rounded-xl border border-line bg-paper p-4">
                                <h4 class="font-display text-sm font-semibold uppercase tracking-wide text-ink-soft">
                                    Gambar Sampul
                                    <span class="font-sans text-[11px] font-medium normal-case tracking-normal text-ink-soft">(opsional)</span>
                                </h4>

                                <div x-data="{ dragging: false }" class="mt-4">
                                    <input id="sampul" x-ref="sampulInput" type="file" wire:model="sampul"
                                           accept="image/jpeg" class="sr-only">

                                    <label for="sampul"
                                           @dragover.prevent="dragging = true"
                                           @dragleave.prevent="dragging = false"
                                           @drop.prevent="dragging = false; $refs.sampulInput.files = $event.dataTransfer.files; $refs.sampulInput.dispatchEvent(new Event('change', { bubbles: true }))"
                                           :class="dragging ? 'border-teal-bright bg-mint' : 'border-line bg-bone'"
                                           class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed p-4 text-center transition">
                                        @if ($sampul)
                                            <img src="{{ $sampul->temporaryUrl() }}"
                                                 alt="Preview gambar sampul"
                                                 class="mb-2 h-32 w-full rounded-md object-cover">
                                            <span class="text-xs font-semibold text-teal-bright">Ganti gambar</span>
                                        @elseif ($existingSampulUrl && ! $removeSampul)
                                            <img src="{{ $existingSampulUrl }}"
                                                 alt="Gambar sampul saat ini"
                                                 class="mb-2 h-32 w-full rounded-md object-cover">
                                            <span class="text-xs font-semibold text-teal-bright">Ganti gambar</span>
                                        @else
                                            <x-admin.icon name="photo" class="h-8 w-8 text-ink-soft" />
                                            <span class="mt-2 text-sm font-medium text-ink-soft">
                                                Tarik foto ke sini atau klik untuk pilih file
                                            </span>
                                            <span class="mt-1 text-xs text-ink-soft">JPG · maks {{ config('cms.image.max_kb') / 1024 }} MB</span>
                                        @endif
                                    </label>

                                    <div wire:loading wire:target="sampul" class="mt-2 text-xs text-ink-soft">
                                        Mengunggah gambar...
                                    </div>

                                    @error('sampul')
                                        <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                                    @enderror

                                    @if ($sampul || ($existingSampulUrl && ! $removeSampul))
                                        <button type="button" wire:click="hapusSampul"
                                                class="mt-2 text-xs font-semibold text-danger-text hover:underline">
                                            Hapus gambar sampul
                                        </button>
                                    @endif
                                </div>
                            </section>
                        </aside>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-2 border-t border-line pt-4 sm:flex-row sm:items-center sm:justify-end">
                        <button type="button" wire:click="closeForm"
                                class="inline-flex w-full items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper sm:w-auto">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="save"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto">
                            <span wire:loading.remove wire:target="save">Simpan Post</span>
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
             role="dialog" aria-modal="true" aria-labelledby="post-delete-title">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="p-5">
                    <h3 id="post-delete-title" class="font-display text-lg font-semibold">
                        Hapus "{{ $deletingJudul }}"?
                    </h3>
                    <p class="mt-2 text-sm text-ink-soft">
                        Tindakan ini tidak dapat dibatalkan. Post, gambar sampul, dan gambar isi yang diunggah lewat editor akan dihapus permanen.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-2">
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
