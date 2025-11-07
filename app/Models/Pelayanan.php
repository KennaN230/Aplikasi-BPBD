<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelayanan extends Model
{
    protected $table = 'tb_kerusakan_pelayanandasar';
    protected $primaryKey = 'id_kerusakan_pelayanandasar';
    protected $fillable = [
        'id_kejadian',
        'id_jenis_kerusakan_pelayanandasar',
        'pelayanan_rr',
        'pelayanan_rs',
        'pelayanan_rb',
        'pelayanan_terendam',
        'taksiran',
    ];
    public $timestamps = false;

    public function kejadian()
    {
        return $this->belongsTo(Kejadian::class, 'id_kejadian', 'id_kejadian');
    }
}
