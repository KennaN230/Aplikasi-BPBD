<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aktivitas_gunung', function (Blueprint $table) {
            $table->id();

            // Data utama
            $table->date('tanggal');                     // wajib, karena di form required
            $table->string('gunung', 150)->nullable();   // nama gunung (opsional)
            $table->text('meteorologi')->nullable();
            $table->text('visual')->nullable();
            $table->text('aktivitas_vulkanik')->nullable();
            $table->text('rekomendasi')->nullable();

            // Info file dokumentasi (opsional)
            $table->string('dokumentasi_mime', 191)->nullable();
            $table->string('dokumentasi_name', 255)->nullable();
            $table->unsignedBigInteger('dokumentasi_size')->nullable();
            $table->string('dokumentasi_path', 255)->nullable();

            $table->timestamps();

            // Index agar filter & pencarian lebih cepat
            $table->index('tanggal', 'ag_tanggal_idx');
            $table->index('gunung',  'ag_gunung_idx');
            $table->index(['tanggal', 'gunung'], 'ag_tanggal_gunung_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_gunung');
    }
};
