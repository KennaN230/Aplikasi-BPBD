<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasGunung extends Model
{
    protected $table = 'aktivitas_gunung';

    protected $fillable = [
        'tanggal',
        'gunung',
        'meteorologi',
        'visual',
        'aktivitas_vulkanik',
        'rekomendasi',
        'dokumentasi_mime',
        'dokumentasi_name',
        'dokumentasi_size',
        'dokumentasi_path',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}