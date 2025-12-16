<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    protected $table = 'tb_kabupaten';
    protected $primaryKey = 'id_kabupaten';
    public $timestamps = false;

    protected $fillable = ['kabupaten'];
}
