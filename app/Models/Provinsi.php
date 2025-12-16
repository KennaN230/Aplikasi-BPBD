<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $table = 'tb_provinsi';
    protected $primaryKey = 'id_provinsi';
    
    protected $fillable = [
        'nama_provinsi',
        'kode_provinsi'
    ];

    public function kabupaten()
    {
        return $this->hasMany(Kabupaten::class, 'id_provinsi');
    }

    public function kejadian()
    {
        return $this->hasMany(Kejadian::class, 'id_provinsi');
    }
}