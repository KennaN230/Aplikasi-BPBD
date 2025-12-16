<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_ttd', function (Blueprint $table) {
            $table->id();
            $table->string('nip_pengawas');
            $table->string('jenis_template')->nullable(); // laporan gelombang, cuaca, dll
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_ttd');
    }
};
