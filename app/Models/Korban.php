<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Korban extends Model
{
    protected $table = 'tb_korban';
    
    // Jika primary key bukan 'id'
    protected $primaryKey = 'id_korban';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_kejadian',
        'id_kategori_korban',
        'id_kategori_umur',
        'L',
        'P',
    ];
    
    // Relasi ke KategoriKorban
    public function kategoriKorban()
    {
        return $this->belongsTo(KategoriKorban::class, 'id_kategori_korban', 'id_kategori_korban');
    }
    
    // Relasi ke KategoriUmur
    public function kategoriUmur()
    {
        return $this->belongsTo(KategoriUmur::class, 'id_kategori_umur', 'id_kategori_umur');
    }
    
    // Relasi ke Kejadian
    public function kejadian()
    {
        return $this->belongsTo(Kejadian::class, 'id_kejadian', 'id_kejadian');
    }
}