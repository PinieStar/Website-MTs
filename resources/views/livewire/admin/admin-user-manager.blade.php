<div class="mx-auto max-w-6xl space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="font-display text-xl font-semibold">Daftar Akun Admin</h2>
            <p class="mt-1 text-sm text-ink-soft">
                Kelola akun yang dapat masuk ke panel admin madrasah.
            </p>
        </div>
        <button type="button" wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover">
            <x-admin.icon name="plus" class="h-4 w-4" />
            Tambah Admin Baru
        </button>
    </div>

    <div class="rounded-xl border border-gold/30 bg-gold/5 p-4">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gold/15 text-gold-text">
                <x-admin.icon name="star" class="h-4 w-4" />
            </span>
            <div>
                <p class="text-sm font-semibold text-teal-deep">Akses khusus Super Admin</p>
                <p class="mt-1 text-sm text-ink-soft">
                    Form ini hanya dapat membuat akun dengan role Admin, dan Anda memilih menu apa saja yang
                    boleh diakses lewat kotak centang. Akun Super Admin baru tidak dapat dibuat dari panel ini
                    dan hanya dibuat manual oleh developer melalui seeder atau tinker.
                </p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-line bg-bone shadow-sm">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-sm">
                <thead class="bg-paper text-left text-xs uppercase tracking-wide text-ink-soft">
                    <tr>
                        <th class="px-5 py-3 font-medium">Nama</th>
                        <th class="px-5 py-3 font-medium">Email</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Akses Menu</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Dibuat</th>
                        <th class="px-5 py-3 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($users as $user)
                        <tr wire:key="admin-user-{{ $user->id }}" class="odd:bg-bone even:bg-paper/40">
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $user->name }}</p>
                                @if ($user->id === auth()->id())
                                    <p class="mt-0.5 inline-flex items-center gap-1 text-[11px] font-semibold text-teal-bright">
                                        <x-admin.icon name="shield" class="h-3 w-3" />
                                        Akun Anda
                                    </p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-ink-soft">{{ $user->email }}</td>
                            <td class="px-5 py-4">
                                @if ($user->isSuperAdmin())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gold/15 px-2.5 py-1 text-xs font-semibold text-gold">
                                        <x-admin.icon name="star" class="h-3.5 w-3.5" />
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-line/60 px-2.5 py-1 text-xs font-medium text-ink-soft">
                                        Admin
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($user->isSuperAdmin())
                                    <span class="text-xs text-ink-soft">Semua menu</span>
                                @elseif (empty($user->permissions))
                                    <span class="text-xs font-medium text-danger-text">Belum ada akses</span>
                                @else
                                    <div class="flex max-w-xs flex-wrap gap-1">
                                        @foreach (\App\Models\User::MODULES as $key => $label)
                                            @if ($user->canAccessModule($key))
                                                <span class="inline-flex rounded-full bg-line/60 px-2 py-0.5 text-[11px] font-medium text-ink-soft">{{ $label }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($user->aktif)
                                    <span class="inline-flex rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold text-success-text">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-line/60 px-2.5 py-1 text-xs font-medium text-ink-soft">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-ink-soft">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    <button type="button" wire:click="openIdentityModal({{ $user->id }})"
                                            class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:bg-paper"
                                            title="Kartu identitas {{ $user->name }}">
                                        <x-admin.icon name="id-card" class="h-4 w-4" />
                                        <span class="sr-only">Kartu identitas {{ $user->name }}</span>
                                    </button>

                                    @if ($user->id === auth()->id())
                                        <span class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft"
                                              title="Akun sendiri tidak dapat dinonaktifkan atau dihapus dari halaman ini">
                                            <x-admin.icon name="lock" class="h-4 w-4" />
                                            <span class="sr-only">Akun dilindungi</span>
                                        </span>
                                    @else
                                        <button type="button" wire:click="toggleAktif({{ $user->id }})"
                                                wire:loading.attr="disabled" wire:target="toggleAktif"
                                                class="inline-flex items-center justify-center rounded-lg border p-2 transition disabled:cursor-not-allowed disabled:opacity-50 {{ $user->aktif ? 'border-line text-ink-soft hover:bg-paper' : 'border-success/30 text-success-text hover:bg-success/10' }}"
                                                title="{{ $user->aktif ? 'Nonaktifkan akun '.$user->name : 'Aktifkan akun '.$user->name }}">
                                            <x-admin.icon name="power" class="h-4 w-4" />
                                            <span class="sr-only">{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->name }}</span>
                                        </button>

                                        @unless ($user->isSuperAdmin())
                                            <button type="button" wire:click="openAccessModal({{ $user->id }})"
                                                    class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:bg-paper"
                                                    title="Atur akses {{ $user->name }}">
                                                <x-admin.icon name="shield" class="h-4 w-4" />
                                                <span class="sr-only">Atur akses {{ $user->name }}</span>
                                            </button>
                                        @endunless

                                        <button type="button" wire:click="openResetModal({{ $user->id }})"
                                                class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:bg-paper"
                                                title="Reset password {{ $user->name }}">
                                            <x-admin.icon name="key" class="h-4 w-4" />
                                            <span class="sr-only">Reset password {{ $user->name }}</span>
                                        </button>

                                        <button type="button" wire:click="confirmDelete({{ $user->id }})"
                                                class="inline-flex items-center justify-center rounded-lg border border-danger/30 p-2 text-danger-text transition hover:bg-danger/10"
                                                title="Hapus akun {{ $user->name }}">
                                            <x-admin.icon name="trash" class="h-4 w-4" />
                                            <span class="sr-only">Hapus akun {{ $user->name }}</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-ink-soft">
                                Belum ada akun admin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Daftar kartu untuk layar HP --}}
        <div class="divide-y divide-line md:hidden">
            @forelse ($users as $user)
                <div wire:key="admin-user-card-{{ $user->id }}" class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-words font-semibold leading-snug text-ink">{{ $user->name }}</p>
                            <p class="truncate text-xs text-ink-soft">{{ $user->email }}</p>
                            @if ($user->id === auth()->id())
                                <p class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-teal-bright">
                                    <x-admin.icon name="shield" class="h-3 w-3" />
                                    Akun Anda
                                </p>
                            @endif
                        </div>
                        @if ($user->aktif)
                            <span class="inline-flex shrink-0 rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold text-success-text">Aktif</span>
                        @else
                            <span class="inline-flex shrink-0 rounded-full bg-line/60 px-2.5 py-1 text-xs font-medium text-ink-soft">Nonaktif</span>
                        @endif
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        @if ($user->isSuperAdmin())
                            <span class="inline-flex items-center gap-1 rounded-full bg-gold/15 px-2.5 py-1 text-xs font-semibold text-gold">
                                <x-admin.icon name="star" class="h-3.5 w-3.5" />
                                Super Admin
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-line/60 px-2.5 py-1 text-xs font-medium text-ink-soft">Admin</span>
                            @if (empty($user->permissions))
                                <span class="text-xs font-medium text-danger-text">Belum ada akses</span>
                            @else
                                @foreach (\App\Models\User::MODULES as $key => $label)
                                    @if ($user->canAccessModule($key))
                                        <span class="inline-flex rounded-full bg-line/60 px-2 py-0.5 text-[11px] font-medium text-ink-soft">{{ $label }}</span>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    </div>

                    <div class="mt-3 flex flex-wrap items-center justify-end gap-2 border-t border-line pt-3">
                        <button type="button" wire:click="openIdentityModal({{ $user->id }})"
                                class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:bg-paper"
                                title="Kartu identitas {{ $user->name }}">
                            <x-admin.icon name="id-card" class="h-4 w-4" />
                            <span class="sr-only">Kartu identitas {{ $user->name }}</span>
                        </button>

                        @if ($user->id === auth()->id())
                            <span class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft"
                                  title="Akun sendiri tidak dapat dinonaktifkan atau dihapus dari halaman ini">
                                <x-admin.icon name="lock" class="h-4 w-4" />
                                <span class="sr-only">Akun dilindungi</span>
                            </span>
                        @else
                            <button type="button" wire:click="toggleAktif({{ $user->id }})"
                                    wire:loading.attr="disabled" wire:target="toggleAktif"
                                    class="inline-flex items-center justify-center rounded-lg border p-2 transition disabled:cursor-not-allowed disabled:opacity-50 {{ $user->aktif ? 'border-line text-ink-soft hover:bg-paper' : 'border-success/30 text-success-text hover:bg-success/10' }}"
                                    title="{{ $user->aktif ? 'Nonaktifkan akun '.$user->name : 'Aktifkan akun '.$user->name }}">
                                <x-admin.icon name="power" class="h-4 w-4" />
                                <span class="sr-only">{{ $user->aktif ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->name }}</span>
                            </button>

                            @unless ($user->isSuperAdmin())
                                <button type="button" wire:click="openAccessModal({{ $user->id }})"
                                        class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:bg-paper"
                                        title="Atur akses {{ $user->name }}">
                                    <x-admin.icon name="shield" class="h-4 w-4" />
                                    <span class="sr-only">Atur akses {{ $user->name }}</span>
                                </button>
                            @endunless

                            <button type="button" wire:click="openResetModal({{ $user->id }})"
                                    class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:bg-paper"
                                    title="Reset password {{ $user->name }}">
                                <x-admin.icon name="key" class="h-4 w-4" />
                                <span class="sr-only">Reset password {{ $user->name }}</span>
                            </button>

                            <button type="button" wire:click="confirmDelete({{ $user->id }})"
                                    class="inline-flex items-center justify-center rounded-lg border border-danger/30 p-2 text-danger-text transition hover:bg-danger/10"
                                    title="Hapus akun {{ $user->name }}">
                                <x-admin.icon name="trash" class="h-4 w-4" />
                                <span class="sr-only">Hapus akun {{ $user->name }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-ink-soft">
                    Belum ada akun admin.
                </div>
            @endforelse
        </div>

        @if ($users->hasPages())
            <div class="border-t border-line px-5 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <p class="text-xs text-ink-soft">
        Menghapus akun tidak menghapus berita, artikel, galeri, atau prestasi yang pernah dibuat.
        Atribusi penulis pada berita/artikel akan menjadi kosong, tetapi isi konten tetap tersimpan.
    </p>

    {{-- Modal tambah admin --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeCreateModal"
             role="dialog" aria-modal="true" aria-labelledby="admin-create-title">
            <div class="mx-auto my-8 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <h3 id="admin-create-title" class="font-display text-lg font-semibold">
                        Tambah Admin Baru
                    </h3>
                    <button type="button" wire:click="closeCreateModal"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="createAdmin" class="space-y-4 p-5">
                    <div>
                        <label for="admin-name" class="block text-sm font-medium text-ink-soft">
                            Nama <span class="text-danger-text">*</span>
                        </label>
                        <input id="admin-name" type="text" wire:model="name" autocomplete="name"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Nama lengkap admin">
                        @error('name')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin-email" class="block text-sm font-medium text-ink-soft">
                            Email <span class="text-danger-text">*</span>
                        </label>
                        <input id="admin-email" type="email" wire:model="email" autocomplete="email"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="nama@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin-password" class="block text-sm font-medium text-ink-soft">
                            Password <span class="text-danger-text">*</span>
                        </label>
                        <input id="admin-password" type="password" wire:model="password" autocomplete="new-password"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin-password-confirmation" class="block text-sm font-medium text-ink-soft">
                            Konfirmasi Password <span class="text-danger-text">*</span>
                        </label>
                        <input id="admin-password-confirmation" type="password" wire:model="password_confirmation"
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Ulangi password">
                        @error('password_confirmation')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <p class="block text-sm font-medium text-ink-soft">Role</p>
                        <div class="mt-1 flex items-center gap-2 rounded-lg border border-line bg-paper px-3 py-2.5">
                            <span class="inline-flex rounded-full bg-line/60 px-2.5 py-1 text-xs font-medium text-ink-soft">
                                Admin
                            </span>
                            <span class="text-xs text-ink-soft">
                                Role ini terkunci. Super Admin tidak dapat dibuat dari form.
                            </span>
                        </div>
                    </div>

                    <x-admin.module-checkboxes model="permissions" id-prefix="akses-baru" />

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeCreateModal"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="createAdmin"
                                class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="createAdmin">Simpan Admin</span>
                            <span wire:loading wire:target="createAdmin">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal kartu identitas admin --}}
    @if ($showIdentityModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeIdentityModal"
             role="dialog" aria-modal="true" aria-labelledby="admin-identity-title">
            <div class="mx-auto my-8 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <div>
                        <h3 id="admin-identity-title" class="font-display text-lg font-semibold">
                            Kartu Identitas Admin
                        </h3>
                        <p class="mt-0.5 text-xs text-ink-soft">
                            Hanya dapat dilihat oleh Super Admin.
                        </p>
                    </div>
                    <button type="button" wire:click="closeIdentityModal"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <div class="space-y-4 p-5">
                    <div class="overflow-hidden rounded-xl border border-line bg-paper shadow-sm">
                        <div class="bg-brand px-5 py-4 text-on-brand">
                            <div class="flex items-center gap-3">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-on-brand/15 font-display text-xl font-bold">
                                    {{ mb_strtoupper(mb_substr($identityName, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate font-display text-lg font-semibold">{{ $identityName }}</p>
                                    <p class="truncate text-xs text-on-brand/80">{{ $identityEmail }}</p>
                                </div>
                            </div>

                            <div class="mt-3 flex flex-wrap items-center gap-1.5">
                                @if ($identityRole === 'super_admin')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gold/20 px-2.5 py-1 text-xs font-semibold text-on-brand">
                                        <x-admin.icon name="star" class="h-3.5 w-3.5" />
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-on-brand/15 px-2.5 py-1 text-xs font-semibold text-on-brand">
                                        Admin
                                    </span>
                                @endif

                                @if ($identityAktif)
                                    <span class="inline-flex rounded-full bg-success/20 px-2.5 py-1 text-xs font-semibold text-on-brand">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-on-brand/15 px-2.5 py-1 text-xs font-semibold text-on-brand/80">
                                        Nonaktif
                                    </span>
                                @endif

                                @if ($identityId === auth()->id())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-on-brand/15 px-2.5 py-1 text-xs font-semibold text-on-brand">
                                        <x-admin.icon name="shield" class="h-3.5 w-3.5" />
                                        Akun Anda
                                    </span>
                                @endif
                            </div>
                        </div>

                        <dl class="divide-y divide-line text-sm">
                            <div class="flex items-start gap-3 px-5 py-3">
                                <dt class="flex w-24 shrink-0 items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-ink-soft">
                                    <x-admin.icon name="user" class="h-3.5 w-3.5" />
                                    Nama
                                </dt>
                                <dd class="min-w-0 flex-1 break-words font-medium text-ink">{{ $identityName }}</dd>
                            </div>
                            <div class="flex items-start gap-3 px-5 py-3">
                                <dt class="flex w-24 shrink-0 items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-ink-soft">
                                    <x-admin.icon name="mail" class="h-3.5 w-3.5" />
                                    Email
                                </dt>
                                <dd class="min-w-0 flex-1 break-all text-ink">{{ $identityEmail }}</dd>
                            </div>
                            <div class="flex items-start gap-3 px-5 py-3">
                                <dt class="flex w-24 shrink-0 items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-ink-soft">
                                    <x-admin.icon name="lock" class="h-3.5 w-3.5" />
                                    Password
                                </dt>
                                <dd class="min-w-0 flex-1">
                                    @if ($identityHasPassword)
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if ($identityPasswordVisible)
                                                <span class="break-all font-mono text-sm text-ink">{{ $identityPassword }}</span>
                                                <button type="button" wire:click="hideIdentityPassword"
                                                        class="inline-flex items-center justify-center rounded-lg border border-line px-2 py-1 text-[11px] font-semibold text-ink-soft transition hover:bg-paper">
                                                    Sembunyikan
                                                </button>
                                            @else
                                                <span class="font-mono text-base tracking-[0.35em] text-ink">••••••••</span>
                                                <button type="button" wire:click="showIdentityPassword"
                                                        class="inline-flex items-center justify-center rounded-lg border border-line px-2 py-1 text-[11px] font-semibold text-ink-soft transition hover:bg-paper">
                                                    Tampilkan
                                                </button>
                                            @endif
                                        </div>
                                        <span class="mt-0.5 block text-[11px] text-ink-soft">
                                            Hanya Super Admin yang dapat melihat. Password disimpan terenkripsi.
                                        </span>
                                    @else
                                        <span class="text-xs font-medium text-ink-soft">Belum tersedia</span>
                                        <span class="mt-0.5 block text-[11px] text-ink-soft">
                                            Password akun ini belum tersimpan. Gunakan tombol Reset Password untuk menyimpan versi terbaru.
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex items-start gap-3 px-5 py-3">
                                <dt class="flex w-24 shrink-0 items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-ink-soft">
                                    <x-admin.icon name="shield" class="h-3.5 w-3.5" />
                                    Akses
                                </dt>
                                <dd class="min-w-0 flex-1">
                                    @if ($identityRole === 'super_admin')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gold/15 px-2.5 py-1 text-xs font-semibold text-gold-text">
                                            <x-admin.icon name="star" class="h-3.5 w-3.5" />
                                            Semua menu
                                        </span>
                                    @elseif (empty($identityPermissions))
                                        <span class="text-xs font-medium text-danger-text">Belum ada akses</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach (\App\Models\User::MODULES as $key => $label)
                                                @if (in_array($key, $identityPermissions, true))
                                                    <span class="inline-flex rounded-full bg-line/60 px-2 py-0.5 text-[11px] font-medium text-ink-soft">{{ $label }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex items-start gap-3 px-5 py-3">
                                <dt class="flex w-24 shrink-0 items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-ink-soft">
                                    <x-admin.icon name="calendar" class="h-3.5 w-3.5" />
                                    Dibuat
                                </dt>
                                <dd class="min-w-0 flex-1 text-ink">{{ $identityCreatedAt }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeIdentityModal"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Tutup
                        </button>

                        @if ($identityId !== auth()->id())
                            @unless ($identityRole === 'super_admin')
                                <button type="button" wire:click="editAccessFromIdentity"
                                        class="inline-flex items-center justify-center rounded-lg border border-line px-4 py-2.5 text-sm font-semibold text-ink-soft transition hover:bg-paper">
                                    Atur Akses
                                </button>
                            @endunless

                            <button type="button" wire:click="resetFromIdentity"
                                    class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover">
                                Reset Password
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal atur akses menu --}}
    @if ($showAccessModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeAccessModal"
             role="dialog" aria-modal="true" aria-labelledby="admin-access-title">
            <div class="mx-auto my-8 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <div>
                        <h3 id="admin-access-title" class="font-display text-lg font-semibold">
                            Atur Akses Menu
                        </h3>
                        <p class="mt-0.5 text-xs text-ink-soft">
                            {{ $accessName }} · {{ $accessEmail }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeAccessModal"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="saveAccess" class="space-y-4 p-5">
                    <x-admin.module-checkboxes model="accessPermissions" id-prefix="akses-ubah" />

                    <div class="rounded-lg border border-line bg-paper px-3 py-2.5 text-xs text-ink-soft">
                        Perubahan langsung berlaku. Menu yang tidak dicentang akan hilang dari panel akun ini
                        dan halamannya tidak dapat dibuka lagi.
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeAccessModal"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="saveAccess"
                                class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveAccess">Simpan Akses</span>
                            <span wire:loading wire:target="saveAccess">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal reset password --}}
    @if ($showResetModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm p-4 sm:p-6"
             wire:click.self="closeResetModal"
             role="dialog" aria-modal="true" aria-labelledby="admin-reset-title">
            <div class="mx-auto my-8 w-full max-w-lg overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="flex items-center justify-between border-b border-line bg-paper px-5 py-4">
                    <div>
                        <h3 id="admin-reset-title" class="font-display text-lg font-semibold">
                            Reset Password
                        </h3>
                        <p class="mt-0.5 text-xs text-ink-soft">
                            {{ $resettingName }} · {{ $resettingEmail }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeResetModal"
                            class="rounded-lg p-2 text-ink-soft transition hover:bg-paper"
                            title="Tutup">
                        <x-admin.icon name="x" class="h-5 w-5" />
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>

                <form wire:submit="resetPassword" class="space-y-4 p-5">
                    <div class="rounded-lg border border-line bg-paper px-3 py-2.5 text-xs text-ink-soft">
                        Password baru langsung berlaku. Sesi login admin tersebut akan dihentikan dan ia harus
                        login ulang dengan password baru.
                    </div>

                    <div>
                        <label for="admin-new-password" class="block text-sm font-medium text-ink-soft">
                            Password Baru <span class="text-danger-text">*</span>
                        </label>
                        <input id="admin-new-password" type="password" wire:model="newPassword"
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Minimal 8 karakter">
                        @error('newPassword')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin-new-password-confirmation" class="block text-sm font-medium text-ink-soft">
                            Konfirmasi Password Baru <span class="text-danger-text">*</span>
                        </label>
                        <input id="admin-new-password-confirmation" type="password" wire:model="newPassword_confirmation"
                               autocomplete="new-password"
                               class="mt-1 block w-full rounded-lg border-line bg-bone text-sm text-ink shadow-sm focus:border-teal-bright focus:ring-teal-bright"
                               placeholder="Ulangi password baru">
                        @error('newPassword_confirmation')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <button type="button" wire:click="closeResetModal"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="resetPassword"
                                class="inline-flex items-center justify-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-on-brand transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                            <span wire:loading wire:target="resetPassword">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal hapus akun --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
             wire:click.self="closeDeleteModal"
             role="dialog" aria-modal="true" aria-labelledby="admin-delete-title">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-line bg-bone shadow-float">
                <div class="p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-danger/10 text-danger-text">
                        <x-admin.icon name="trash" class="h-5 w-5" />
                    </div>

                    <h3 id="admin-delete-title" class="mt-4 font-display text-lg font-semibold">
                        Hapus akun "{{ $deletingName }}"?
                    </h3>
                    <p class="mt-1 text-sm text-ink-soft">{{ $deletingEmail }}</p>

                    <div class="mt-4 space-y-2 rounded-lg border border-danger/20 bg-danger/5 px-3 py-3 text-sm text-ink-soft">
                        <p>
                            Akun akan dihapus permanen dan tidak dapat login lagi. Tindakan ini tidak dapat
                            dibatalkan.
                        </p>
                        @if ($deletingPostCount > 0)
                            <p>
                                Akun ini memiliki
                                <span class="font-semibold text-ink">{{ $deletingPostCount }} berita/artikel</span>.
                                Konten tersebut <span class="font-semibold text-ink">TIDAK</span> akan dihapus;
                                hanya atribusi penulisnya yang menjadi kosong.
                            </p>
                        @else
                            <p>
                                Berita, artikel, galeri, dan prestasi yang pernah dibuat akun ini
                                <span class="font-semibold text-ink">tidak akan ikut terhapus</span>.
                            </p>
                        @endif
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-2">
                        <button type="button" wire:click="closeDeleteModal"
                                class="inline-flex items-center justify-center rounded-lg border border-line bg-bone px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-paper">
                            Batal
                        </button>
                        <button type="button" wire:click="deleteUser" wire:loading.attr="disabled" wire:target="deleteUser"
                                class="inline-flex items-center justify-center rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-danger/90 disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="deleteUser">Ya, Hapus Akun</span>
                            <span wire:loading wire:target="deleteUser">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
