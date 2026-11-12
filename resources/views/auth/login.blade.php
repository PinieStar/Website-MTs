<x-auth-layout title="Masuk">
    <div class="mb-7">
        <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-teal-700">
            <span class="h-1.5 w-1.5 rounded-full bg-teal-700"></span>
            Portal Admin
        </span>

        <h2 class="mt-4 font-display text-2xl font-bold tracking-tight text-teal-950 sm:text-[1.75rem]">
            Masuk ke panel admin
        </h2>
        <p class="mt-2 text-sm leading-relaxed text-ink-soft">
            Gunakan email dan kata sandi akun admin Anda untuk melanjutkan.
        </p>
    </div>

    <x-auth-session-status class="mb-5 rounded-lg border border-success/20 bg-success/5 px-3.5 py-2.5"
                           :status="session('status')" />

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-danger/20 bg-danger/5 px-3.5 py-3 text-danger" role="alert">
            <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 8v4" />
                <path d="M12 16h.01" />
            </svg>
            <div class="text-sm">
                <p class="font-semibold">Tidak dapat masuk.</p>
                <p class="mt-0.5 text-danger/80">
                    Periksa kembali data yang Anda masukkan, lalu coba lagi.
                </p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}"
          x-data="{ showPassword: false, submitting: false }"
          @submit="submitting = true"
          class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Email" />

            <div class="relative mt-1.5">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink-soft/60">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="20" height="16" x="2" y="4" rx="2" />
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                    </svg>
                </span>

                <input id="email" name="email" type="email" value="{{ old('email') }}"
                       placeholder="nama@email.com"
                       required autofocus autocomplete="username"
                       @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                       class="block w-full rounded-lg border-line bg-bone py-2.5 pl-10 pr-3 text-sm text-ink shadow-sm transition placeholder:text-ink-soft/45 focus:border-teal-700 focus:ring-teal-700 @error('email') border-danger focus:border-danger focus:ring-danger @enderror">
            </div>

            @error('email')
                <p id="email-error" class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kata sandi --}}
        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Kata sandi" />

                <span class="text-right text-xs text-ink-soft">
                    Lupa kata sandi? Hubungi <span class="font-semibold text-ink">Super Admin</span>.
                </span>
            </div>

            <div class="relative mt-1.5">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-ink-soft/60">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                </span>

                <input id="password" name="password" type="password"
                       placeholder="Masukkan kata sandi"
                       required autocomplete="current-password"
                       x-bind:type="showPassword ? 'text' : 'password'"
                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                       class="block w-full rounded-lg border-line bg-bone py-2.5 pl-10 pr-11 text-sm text-ink shadow-sm transition placeholder:text-ink-soft/45 focus:border-teal-700 focus:ring-teal-700 @error('password') border-danger focus:border-danger focus:ring-danger @enderror">

                <button type="button" @click="showPassword = !showPassword"
                        x-bind:aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-0 flex items-center rounded-md px-3 text-ink-soft/70 transition hover:text-teal-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-700">
                    <svg x-show="! showPassword" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                        <path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                        <path d="m2 2 20 20" />
                        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24" />
                    </svg>
                </button>
            </div>

            @error('password')
                <p id="password-error" class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ingat saya --}}
        <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2.5 select-none">
            <input id="remember_me" name="remember" type="checkbox"
                   class="h-4 w-4 rounded border-line bg-bone text-teal-700 shadow-sm focus:ring-teal-700">
            <span class="text-sm text-ink-soft">Ingat saya di perangkat ini</span>
        </label>

        {{-- Tombol masuk --}}
        <button type="submit" :disabled="submitting"
                class="group flex w-full items-center justify-center gap-2 rounded-lg bg-teal-950 px-4 py-3 text-sm font-semibold text-paper shadow-float transition hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-700 focus:ring-offset-2 focus:ring-offset-paper disabled:cursor-not-allowed disabled:opacity-70">
            <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"
                 aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-90" fill="currentColor"
                      d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4zm2 5.29A8 8 0 0 1 4 12H0c0 3.04 1.14 5.82 3 7.94l3-2.65z" />
            </svg>
            <span x-text="submitting ? 'Memproses…' : 'Masuk ke panel'">Masuk ke panel</span>
            <svg x-show="! submitting" class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14" />
                <path d="m12 5 7 7-7 7" />
            </svg>
        </button>
    </form>

    <div class="mt-6 flex items-start gap-2.5 rounded-lg border border-line-soft bg-bone/60 px-3.5 py-3 text-xs leading-relaxed text-ink-soft">
        <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
        </svg>
        <p>
            Area terbatas khusus pengelola madrasah. Jangan bagikan akses akun Anda kepada orang lain.
        </p>
    </div>
</x-auth-layout>
