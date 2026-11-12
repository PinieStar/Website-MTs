<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUserManager extends Component
{
    use WithPagination;

    public bool $showCreateModal = false;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Modul yang dicentang untuk akun baru (kunci dari User::MODULES).
     *
     * @var array<int, string>
     */
    public array $permissions = [];

    public bool $showAccessModal = false;

    public ?int $accessId = null;

    public string $accessName = '';

    public string $accessEmail = '';

    /**
     * Modul yang dicentang saat mengubah akses akun yang sudah ada.
     *
     * @var array<int, string>
     */
    public array $accessPermissions = [];

    public bool $showIdentityModal = false;

    public ?int $identityId = null;

    public string $identityName = '';

    public string $identityEmail = '';

    public string $identityRole = '';

    public bool $identityAktif = true;

    public string $identityCreatedAt = '';

    /**
     * Modul yang dimiliki akun yang sedang dilihat pada kartu identitas.
     *
     * @var array<int, string>
     */
    public array $identityPermissions = [];

    public bool $identityPasswordVisible = false;

    public string $identityPassword = '';

    public bool $identityHasPassword = false;

    public bool $showResetModal = false;

    public ?int $resettingId = null;

    public string $resettingName = '';

    public string $resettingEmail = '';

    public string $newPassword = '';

    public string $newPassword_confirmation = '';

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

    public string $deletingEmail = '';

    public int $deletingPostCount = 0;

    /**
     * Lindungi setiap request Livewire, bukan hanya permintaan halaman awal.
     */
    public function boot(): void
    {
        Gate::authorize('manage-admins');
    }

    public function openIdentityModal(int $id): void
    {
        $user = User::findOrFail($id);

        $this->identityId = $user->id;
        $this->identityName = $user->name;
        $this->identityEmail = $user->email;
        $this->identityRole = $user->role;
        $this->identityAktif = (bool) $user->aktif;
        $this->identityCreatedAt = $user->created_at?->format('d M Y') ?? '-';
        $this->identityPermissions = $this->normalizePermissions($user->permissions ?? []);
        $this->loadIdentityPassword($user);
        $this->resetValidation();
        $this->showIdentityModal = true;
    }

    public function closeIdentityModal(): void
    {
        $this->showIdentityModal = false;
        $this->reset([
            'identityId',
            'identityName',
            'identityEmail',
            'identityRole',
            'identityAktif',
            'identityCreatedAt',
            'identityPermissions',
            'identityPasswordVisible',
            'identityPassword',
            'identityHasPassword',
        ]);
        $this->resetValidation();
    }

    public function hideIdentityPassword(): void
    {
        $this->identityPassword = '';
        $this->identityPasswordVisible = false;
    }

    public function showIdentityPassword(): void
    {
        if ($this->identityId === null) {
            return;
        }

        $user = User::find($this->identityId);

        if ($user === null) {
            return;
        }

        $this->loadIdentityPassword($user);
    }

    public function resetFromIdentity(): void
    {
        if ($this->identityId === null) {
            return;
        }

        $id = $this->identityId;
        $this->closeIdentityModal();
        $this->openResetModal($id);
    }

    public function editAccessFromIdentity(): void
    {
        if ($this->identityId === null) {
            return;
        }

        $id = $this->identityId;
        $this->closeIdentityModal();
        $this->openAccessModal($id);
    }

    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function createAdmin(): void
    {
        $this->name = trim($this->name);
        $this->email = Str::lower(trim($this->email));

        $validated = $this->validate(
            $this->createRules(),
            $this->messages(),
            $this->validationAttributes(),
        );

        $created = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'password_cadangan' => $validated['password'],
            'role' => 'admin',
            'permissions' => $this->normalizePermissions($validated['permissions']),
            'aktif' => true,
        ]);

        $created->forceFill(['email_verified_at' => now()])->save();

        $this->closeCreateModal();
        $this->resetPage();

        $this->dispatch(
            'toast',
            message: 'Akun admin berhasil dibuat. Akun dapat langsung digunakan untuk login.',
            type: 'success',
        );
    }

    public function openAccessModal(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            $this->dispatch(
                'toast',
                message: 'Super Admin selalu memiliki akses ke semua menu.',
                type: 'danger',
            );

            return;
        }

        $this->accessId = $user->id;
        $this->accessName = $user->name;
        $this->accessEmail = $user->email;
        $this->accessPermissions = $this->normalizePermissions($user->permissions ?? []);
        $this->resetValidation();
        $this->showAccessModal = true;
    }

    public function closeAccessModal(): void
    {
        $this->showAccessModal = false;
        $this->reset(['accessId', 'accessName', 'accessEmail', 'accessPermissions']);
        $this->resetValidation();
    }

    public function saveAccess(): void
    {
        if ($this->accessId === null) {
            return;
        }

        $target = User::find($this->accessId);

        if ($target === null || $target->isSuperAdmin()) {
            $this->closeAccessModal();
            $this->dispatch(
                'toast',
                message: 'Akun yang akan diubah aksesnya tidak ditemukan.',
                type: 'danger',
            );

            return;
        }

        $validated = $this->validate(
            ['accessPermissions' => $this->permissionRules()['permissions'], 'accessPermissions.*' => $this->permissionRules()['permissions.*']],
            $this->messages(),
            $this->validationAttributes(),
        );

        $target->forceFill([
            'permissions' => $this->normalizePermissions($validated['accessPermissions']),
        ])->save();

        $name = $target->name;

        $this->closeAccessModal();

        $this->dispatch(
            'toast',
            message: "Akses akun {$name} berhasil diperbarui. Perubahan langsung berlaku.",
            type: 'success',
        );
    }

    public function toggleAktif(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch(
                'toast',
                message: 'Anda tidak dapat menonaktifkan akun sendiri.',
                type: 'danger',
            );

            return;
        }

        $user->aktif = ! $user->aktif;

        if (! $user->aktif) {
            $user->remember_token = Str::random(60);
        }

        $user->save();

        if (! $user->aktif) {
            $this->invalidateSessions($user->id);
        }

        $this->dispatch(
            'toast',
            message: $user->aktif
                ? "Akun {$user->name} berhasil diaktifkan kembali."
                : "Akun {$user->name} berhasil dinonaktifkan. Sesi login aktifnya telah dihentikan.",
            type: 'success',
        );
    }

    public function openResetModal(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch(
                'toast',
                message: 'Gunakan menu profil untuk mengganti password akun Anda sendiri.',
                type: 'danger',
            );

            return;
        }

        $this->resettingId = $user->id;
        $this->resettingName = $user->name;
        $this->resettingEmail = $user->email;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->resetValidation();
        $this->showResetModal = true;
    }

    public function closeResetModal(): void
    {
        $this->showResetModal = false;
        $this->reset([
            'resettingId',
            'resettingName',
            'resettingEmail',
            'newPassword',
            'newPassword_confirmation',
        ]);
        $this->resetValidation();
    }

    public function resetPassword(): void
    {
        if ($this->resettingId === null) {
            return;
        }

        $target = User::find($this->resettingId);

        if ($target === null) {
            $this->closeResetModal();
            $this->dispatch(
                'toast',
                message: 'Akun yang akan direset tidak ditemukan.',
                type: 'danger',
            );

            return;
        }

        if ($target->id === auth()->id()) {
            $this->closeResetModal();
            $this->dispatch(
                'toast',
                message: 'Gunakan menu profil untuk mengganti password akun Anda sendiri.',
                type: 'danger',
            );

            return;
        }

        $validated = $this->validate(
            $this->resetPasswordRules(),
            $this->messages(),
            $this->validationAttributes(),
        );

        $target->forceFill([
            'password' => Hash::make($validated['newPassword']),
            'password_cadangan' => $validated['newPassword'],
            'remember_token' => Str::random(60),
        ])->save();

        $this->invalidateSessions($target->id);

        $name = $target->name;

        $this->closeResetModal();

        $this->dispatch(
            'toast',
            message: "Password akun {$name} berhasil direset. Admin tersebut harus login ulang dengan password baru.",
            type: 'success',
        );
    }

    public function confirmDelete(int $id): void
    {
        $user = User::withCount('posts')->findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch(
                'toast',
                message: 'Anda tidak dapat menghapus akun sendiri.',
                type: 'danger',
            );

            return;
        }

        $this->deletingId = $user->id;
        $this->deletingName = $user->name;
        $this->deletingEmail = $user->email;
        $this->deletingPostCount = (int) $user->posts_count;
        $this->resetValidation();
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->reset([
            'deletingId',
            'deletingName',
            'deletingEmail',
            'deletingPostCount',
        ]);
        $this->resetValidation();
    }

    public function deleteUser(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        if ($this->deletingId === auth()->id()) {
            $this->closeDeleteModal();
            $this->dispatch(
                'toast',
                message: 'Anda tidak dapat menghapus akun sendiri.',
                type: 'danger',
            );

            return;
        }

        $user = User::find($this->deletingId);

        if ($user === null) {
            $this->closeDeleteModal();
            $this->dispatch(
                'toast',
                message: 'Akun yang akan dihapus tidak ditemukan.',
                type: 'danger',
            );

            return;
        }

        $name = $user->name;

        DB::transaction(function () use ($user): void {
            Post::where('penulis_id', $user->id)->update(['penulis_id' => null]);
            $user->delete();
        });

        $this->invalidateSessions($user->id);

        $this->closeDeleteModal();
        $this->resetPage();

        $this->dispatch(
            'toast',
            message: "Akun {$name} berhasil dihapus. Berita, artikel, galeri, dan prestasi yang pernah dibuat tetap tersimpan.",
            type: 'success',
        );
    }

    /**
     * Pakai tampilan pagination bertema situs untuk komponen Livewire ini.
     */
    public function paginationView(): string
    {
        return 'vendor.pagination.site-livewire';
    }

    public function render(): View
    {
        return view('livewire.admin.admin-user-manager', [
            'users' => User::query()
                ->orderByRaw("case when role = 'super_admin' then 0 else 1 end")
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function createRules(): array
    {
        return [
            ...$this->permissionRules(),
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /**
     * Aturan untuk centang akses: minimal satu modul dan hanya modul yang dikenal.
     *
     * @return array<string, mixed>
     */
    private function permissionRules(): array
    {
        return [
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', Rule::in(array_keys(User::MODULES))],
        ];
    }

    /**
     * Buang nilai tak dikenal/duplikat dan urutkan sesuai daftar modul.
     *
     * @param  array<int, mixed>  $permissions
     * @return array<int, string>
     */
    private function normalizePermissions(array $permissions): array
    {
        return array_values(array_intersect(array_keys(User::MODULES), $permissions));
    }

    /**
     * Muat salinan password terenkripsi untuk kartu identitas.
     *
     * Nilai asli hanya disimpan di properti selama modal dibuka, dan
     * properti tersebut dikosongkan lagi saat password disembunyikan.
     */
    private function loadIdentityPassword(User $user): void
    {
        $this->identityHasPassword = $user->getRawOriginal('password_cadangan') !== null;
        $this->identityPassword = '';
        $this->identityPasswordVisible = false;

        if (! $this->identityHasPassword) {
            return;
        }

        try {
            $this->identityPassword = (string) $user->password_cadangan;
            $this->identityPasswordVisible = true;
        } catch (DecryptException) {
            $this->identityHasPassword = false;
            $this->dispatch(
                'toast',
                message: 'Password tersimpan tidak dapat dibaca. Silakan reset password akun ini.',
                type: 'danger',
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function resetPasswordRules(): array
    {
        return [
            'newPassword' => ['required', 'string', Password::defaults(), 'confirmed'],
            'newPassword_confirmation' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'permissions.required' => 'Pilih minimal satu menu yang boleh diakses.',
            'permissions.min' => 'Pilih minimal satu menu yang boleh diakses.',
            'permissions.*.in' => 'Menu yang dipilih tidak valid.',
            'accessPermissions.required' => 'Pilih minimal satu menu yang boleh diakses.',
            'accessPermissions.min' => 'Pilih minimal satu menu yang boleh diakses.',
            'accessPermissions.*.in' => 'Menu yang dipilih tidak valid.',
            'newPassword.required' => 'Password baru wajib diisi.',
            'newPassword.min' => 'Password baru minimal 8 karakter.',
            'newPassword.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'newPassword_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'password_confirmation' => 'konfirmasi password',
            'permissions' => 'akses menu',
            'accessPermissions' => 'akses menu',
            'newPassword' => 'password baru',
            'newPassword_confirmation' => 'konfirmasi password baru',
        ];
    }

    private function resetCreateForm(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'permissions']);
        $this->resetValidation();
    }

    private function invalidateSessions(int $userId): void
    {
        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $userId)
            ->delete();
    }
}
