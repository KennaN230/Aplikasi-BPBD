<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sarprass extends Model
{
    protected $table = 'tb_jenis_kerusakan_sarpras';
    protected $primaryKey = 'id_jenis_kerusakan_sarpras';
    protected $fillable = ['jenis_kerusakan_sarpras'];
}
