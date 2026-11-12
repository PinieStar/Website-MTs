<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom urutan dari tabel galleries (album) saja.
     *
     * Daftar album kini selalu diurutkan otomatis berdasarkan
     * created_at terbaru. Kolom urutan di gallery_images TIDAK disentuh
     * karena foto di dalam satu album tetap diatur manual oleh admin.
     */
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['urutan']);
            $table->dropColumn('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->integer('urutan')->default(0);
            $table->index('urutan');
        });
    }
};
