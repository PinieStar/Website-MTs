<section aria-labelledby="profil-keamanan-heading">
    <header class="flex items-start gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-mint text-teal-bright">
            <x-admin.icon name="lock" class="h-5 w-5" />
        </span>
        <div>
            <h2 id="profil-keamanan-heading" class="font-display text-lg font-semibold text-teal-deep">
                Keamanan akun
            </h2>
            <p class="mt-1 text-sm leading-relaxed text-ink-soft">
                Ganti kata sandi secara berkala agar akun panel tetap aman.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Kata sandi saat ini" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                          class="mt-1.5 block w-full"
                          autocomplete="current-password" />
            <x-input-error class="mt-1.5" :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="update_password_password" value="Kata sandi baru" />
                <x-text-input id="update_password_password" name="password" type="password"
                              class="mt-1.5 block w-full"
                              autocomplete="new-password" />
                <x-input-error class="mt-1.5" :messages="$errors->updatePassword->get('password')" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" value="Ulangi kata sandi baru" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                              class="mt-1.5 block w-full"
                              autocomplete="new-password" />
                <x-input-error class="mt-1.5" :messages="$errors->updatePassword->get('password_confirmation')" />
            </div>
        </div>

        <p class="flex items-start gap-2 rounded-lg bg-paper/70 px-3.5 py-2.5 text-xs leading-relaxed text-ink-soft">
            <x-admin.icon name="shield" class="mt-0.5 h-4 w-4 shrink-0 text-teal-bright" />
            Gunakan minimal 8 karakter dan hindari kata sandi yang sama dengan akun lain.
        </p>

        <div class="flex flex-wrap items-center gap-3 border-t border-line-soft pt-5">
            <x-primary-button>
                <x-admin.icon name="lock" class="mr-2 h-4 w-4" />
                Perbarui kata sandi
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 3500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-success-text">
                    <x-admin.icon name="check" class="h-4 w-4" />
                    Kata sandi berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
