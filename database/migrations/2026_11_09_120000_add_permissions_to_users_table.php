<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar modul yang akses-nya bisa diatur per akun admin.
     * Harus sama dengan array_keys(User::MODULES); ditulis ulang di sini
     * supaya migrasi tetap berjalan walaupun kelas User berubah nanti.
     *
     * @var array<int, string>
     */
    private array $modules = ['berita', 'artikel', 'galeri', 'prestasi', 'statistik'];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('role');
        });

        // Admin yang sudah ada tetap bisa mengakses semua menu seperti sebelumnya,
        // supaya tidak tiba-tiba terkunci. Super Admin bisa mempersempitnya kemudian.
        DB::table('users')
            ->where('role', 'admin')
            ->update(['permissions' => json_encode($this->modules)]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};
