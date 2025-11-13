<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gempa extends Model
{
    use HasFactory;

    protected $table = 'gempa'; // pastikan sesuai nama tabel di database

    protected $fillable = [
        'tanggal',
        'sr',
        'waktu',
        'gempa',
        'latitude',
        'longitude',
        'keterangan',
    ];
}
