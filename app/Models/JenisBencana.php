<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisBencana extends Model
{
    protected $table = 'tb_jenis_bencana';

    protected $primaryKey = 'id_jenis_bencana';

    public $timestamps = false; // tabel kamu tidak punya created_at / updated_at

    protected $fillable = [
        'jenis_bencana',
        'id_klasifikasi_bencana',
    ];

    public function klasifikasi()
{
    return $this->belongsTo(KlasifikasiBencana::class, 'id_klasifikasi_bencana');
}

}
