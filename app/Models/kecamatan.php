<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = 'tb_kecamatan';
    
    protected $primaryKey = 'id_kecamatan';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kecamatan',
        'id_kabupaten',
        // tambahkan kolom lain jika ada
    ];
    
    /**
     * Relasi ke Desa
     */
    public function desa()
    {
        return $this->hasMany(Desa::class, 'id_kecamatan', 'id_kecamatan');
    }
    
    /**
     * Relasi ke Rain (Curah Hujan)
     */
    public function rains()
    {
        return $this->hasMany(Rain::class, 'id_kecamatan', 'id_kecamatan');
    }
}