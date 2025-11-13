<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitikPanas extends Model
{
    use HasFactory;

    protected $table = 'titik_panas';

    protected $fillable = [
        'tanggal',
        'titik_panas',
        'latitude',
        'longitude',
        'kecamatan',
        'satelit',
        'waktu',
        'tingkat_kepercayaan',
        'keterangan'
    ];
}
