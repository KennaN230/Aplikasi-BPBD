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
        Schema::create('hujans', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan');
            $table->integer('hari_hujan');
            $table->integer('hari_tidak_hujan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hujans');
    }
};
