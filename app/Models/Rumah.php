<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rumah extends Model
{
    protected $table = 'tb_kerusakan_rumah';
    protected $primaryKey = 'id_kerusakan_rumah';
    public $timestamps = false;

    protected $fillable = [
        'id_kejadian',
        'rmh_rr',
        'rmh_rs',
        'rmh_rb',
        'terendam',
        'kerugian'
    ];

    public function kejadian()
    {
        return $this->belongsTo(Kejadian::class, 'id_kejadian', 'id_kejadian');
    }
}