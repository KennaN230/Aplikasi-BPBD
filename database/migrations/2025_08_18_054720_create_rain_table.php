<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rain', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan');
            $table->integer('hari_hujan')->default(0);
            $table->integer('hari_tidak_hujan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rain');
    }
};



class Rain extends Model
{
    use HasFactory;

    protected $table = 'rain'; // supaya pakai tabel 'rain', bukan 'rains'
    protected $fillable = ['kecamatan', 'hari_hujan', 'hari_tidak_hujan'];
}
