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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['berita', 'artikel']);
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan')->nullable();
            $table->longText('isi');
            $table->enum('kategori', ['berita', 'pengumuman', 'kegiatan'])->nullable();
            $table->enum('status', ['draft', 'publish'])->default('draft');
            $table->boolean('sematkan')->default(false);
            $table->date('tanggal_publish');
            $table->foreignId('penulis_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['status', 'tanggal_publish']);
            $table->index('sematkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
