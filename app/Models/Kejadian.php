<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kejadian extends Model
{
    protected $table = 'tb_kejadian';
    protected $primaryKey = 'id_kejadian';
    public $timestamps = false;

    protected $fillable = [
        'id_jenis_bencana',
        'id_nama_kejadian',
        'tanggal',
        'waktu',
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_desa',
        'longitude',
        'latitude',
        'penyebab',
        'kronologi',
        'deskripsi',
        'sumber',
        'kondisi',
        'mutakhir',
        'id_status_darurat',
        'upaya',
        'dokumentasi',
        'sebaran_dampak_kib'
    ];
}
