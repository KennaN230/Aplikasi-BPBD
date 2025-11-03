<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sarpras extends Model
{
    protected $table = 'tb_kerusakan_sarpras';
    protected $primaryKey = 'id_kerusakan_sarpras';
    protected $fillable = ['id_kejadian', 'id_jenis_kerusakan_sarpras', 'rr', 'rs', 'rb', 'terendam', 'taksiran'];
    public $timestamps = false; 

    public function kejadian()
    {
        return $this->belongsTo(Kejadian::class, 'id_kejadian', 'id_kejadian');
    }
}