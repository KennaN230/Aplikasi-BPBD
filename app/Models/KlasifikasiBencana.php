<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiBencana extends Model
{
    protected $table = 'tb_klasifikasi_bencana';
    protected $primaryKey = 'id_klasifikasi_bencana';
    public $timestamps = false;

    protected $fillable = ['klasifikasi_bencana'];

    public function jenis()
    {
        return $this->hasMany(JenisBencana::class, 'id_klasifikasi_bencana');
    }
}
