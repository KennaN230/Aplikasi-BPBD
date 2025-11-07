<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'tb_kecamatan';
    protected $primaryKey = 'id_kecamatan';
    public $timestamps = false;

    protected $fillable = ['nama_kecamatan'];

    public function kejadian()
    {
        return $this->hasMany(Kejadian::class, 'id_kecamatan', 'id_kecamatan');
    }
}
