<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'tb_desa';
    protected $primaryKey = 'id_desa';
    public $timestamps = false;

    protected $fillable = ['id_kecamatan', 'desa', 'latitude', 'longitude'];
}
