<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('peringkat');
            $table->enum('tingkat', ['madrasah', 'kecamatan', 'kabupaten', 'provinsi', 'nasional']);
            $table->enum('bidang', ['akademik', 'keagamaan', 'olahraga', 'seni']);
            $table->year('tahun');
            $table->string('penyelenggara')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index(['tingkat', 'bidang', 'tahun']);
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
