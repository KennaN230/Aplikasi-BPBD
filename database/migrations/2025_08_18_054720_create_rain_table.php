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
    Schema::create('rains', function (Blueprint $table) {
        $table->id();
        $table->date('hari_tanggal'); // ✅ Tambahan kolom tanggal
        $table->string('kecamatan');
        $table->integer('hari_hujan')->default(0);
        $table->integer('hari_tidak_hujan')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rains');
    }
};
