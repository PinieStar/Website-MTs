<section aria-labelledby="profil-hapus-akun-heading">
    <header class="flex items-start gap-3">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-danger/10 text-danger-text">
            <x-admin.icon name="alert" class="h-5 w-5" />
        </span>
        <div>
            <h2 id="profil-hapus-akun-heading" class="font-display text-base font-semibold text-teal-deep">
                Hapus akun
            </h2>
            <p class="mt-1 text-xs leading-relaxed text-ink-soft">
                Akun dan seluruh datanya akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                     class="mt-4 w-full">
        <x-admin.icon name="trash" class="mr-2 h-4 w-4" />
        Hapus akun saya
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-start gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-danger/10 text-danger-text">
                    <x-admin.icon name="alert" class="h-5 w-5" />
                </span>
                <div>
                    <h2 class="font-display text-lg font-semibold text-teal-deep">
                        Yakin ingin menghapus akun?
                    </h2>
                    <p class="mt-1 text-sm leading-relaxed text-ink-soft">
                        Setelah dihapus, akun ini tidak dapat dipulihkan. Masukkan kata sandi Anda
                        untuk mengonfirmasi.
                    </p>
                </div>
            </div>

            <div class="mt-5">
                <x-input-label for="delete_account_password" value="Kata sandi" class="sr-only" />

                <x-text-input id="delete_account_password" name="password" type="password"
                              class="block w-full"
                              placeholder="Masukkan kata sandi Anda" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-danger-button>
                    <x-admin.icon name="trash" class="mr-2 h-4 w-4" />
                    Ya, hapus akun
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
