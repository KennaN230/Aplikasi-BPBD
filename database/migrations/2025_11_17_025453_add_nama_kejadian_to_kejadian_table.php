<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tb_kejadian', function (Blueprint $table) {
        $table->string('nama_kejadian')->nullable()->after('id_nama_kejadian');
    });
}

public function down()
{
    Schema::table('tb_kejadian', function (Blueprint $table) {
        $table->dropColumn('nama_kejadian');
    });
}

};
