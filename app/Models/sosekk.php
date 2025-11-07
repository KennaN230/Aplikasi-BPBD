<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sosekk extends Model
{
    protected $table = 'tb_jenis_kerusakan_sosek';
    protected $primaryKey = 'id_jenis_kerusakan_sosek';
    protected $fillable = ['jenis_kerusakan_sosek'];
}
