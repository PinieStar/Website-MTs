<section aria-labelledby="profil-informasi-heading">
    <header class="flex items-start gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-mint text-teal-bright">
            <x-admin.icon name="user" class="h-5 w-5" />
        </span>
        <div>
            <h2 id="profil-informasi-heading" class="font-display text-lg font-semibold text-teal-deep">
                Informasi profil
            </h2>
            <p class="mt-1 text-sm leading-relaxed text-ink-soft">
                Perbarui nama dan alamat email yang Anda gunakan untuk masuk ke panel.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="name" value="Nama lengkap" />
                <x-text-input id="name" name="name" type="text"
                              class="mt-1.5 block w-full"
                              :value="old('name', $user->name)"
                              required autofocus autocomplete="name" />
                <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" value="Alamat email" />
                <x-text-input id="email" name="email" type="email"
                              class="mt-1.5 block w-full"
                              :value="old('email', $user->email)"
                              required autocomplete="username" />
                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

                <p class="mt-1.5 text-xs text-ink-soft">
                    Email ini dipakai untuk masuk dan menerima notifikasi.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-line-soft pt-5">
            <x-primary-button>
                <x-admin.icon name="check" class="mr-2 h-4 w-4" />
                Simpan perubahan
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 3500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-success-text">
                    <x-admin.icon name="check" class="h-4 w-4" />
                    Profil berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
