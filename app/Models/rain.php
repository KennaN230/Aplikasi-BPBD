<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rain extends Model
{
    protected $table = 'rain'; // atau 'tb_curah_hujan' sesuai nama tabel
    
    // Jika primary key bukan 'id'
    protected $primaryKey = 'id'; // ganti sesuai struktur
    
    protected $fillable = [
        'hari_tanggal',
        'id_kecamatan',
        'hari_hujan',
        'hari_tidak_hujan',
        'intensitas',
    ];
    
    // Cast tanggal
    protected $casts = [
        'hari_tanggal' => 'date',
    ];
    
    /**
     * Relasi ke Kecamatan
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }
}