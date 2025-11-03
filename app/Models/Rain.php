<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rain extends Model
{
    protected $table = 'rain';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['hari_tanggal','kecamatan','hari_hujan','hari_tidak_hujan'];

    protected $casts = [
        'hari_tanggal' => 'date',
        'hari_hujan' => 'integer',
        'hari_tidak_hujan' => 'integer',
    ];
}
