<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NamaKejadian extends Model
{
    protected $table = 'tb_nama_kejadian';
    protected $primaryKey = 'id_nama_kejadian';
    public $timestamps = false;

    protected $fillable = ['nama_kejadian'];

    public function kejadian()
    {
        return $this->hasMany(Kejadian::class, 'id_nama_kejadian', 'id_nama_kejadian');
    }
}