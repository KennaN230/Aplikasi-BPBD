<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sosek extends Model
{
    protected $table = 'tb_kerusakan_sosek';
    protected $primaryKey = 'id_kerusakan_sosek';
    protected $fillable = ['id_kejadian', 'id_jenis_kerusakan_sosek', 'rr', 'rs', 'rb', 'terendam', 'kerugian'];
    public $timestamps = false; 

    public function kejadian()
    {
        return $this->belongsTo(Kejadian::class, 'id_kejadian', 'id_kejadian');
    }
}