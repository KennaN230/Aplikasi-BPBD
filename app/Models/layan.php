<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class layan extends Model
{
    protected $table = 'tb_jenis_kerusakan_pelayanandasar';
    protected $primaryKey = 'id_jenis_kerusakan_pelayanandasar';
    protected $fillable = ['jenis_kerusakan_pelayanandasar'];
}
