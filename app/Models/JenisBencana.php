<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBencana extends Model
{
    protected $table = 'tb_jenis_bencana'; // sesuaikan nama tabel di database
    protected $primaryKey = 'id_jenis_bencana';
    public $timestamps = false;

    protected $fillable = [
        'id_jenis_bencana',
        'nama_bencana',
    ];
}
