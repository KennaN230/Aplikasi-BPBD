<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Korban extends Model
{
    use HasFactory;

    protected $table = 'tb_korban';
    protected $primaryKey = 'id_korban';
    public $timestamps = false; // kalau tabel tidak punya created_at / updated_at

    protected $fillable = [
        'id_kejadian',
        'id_kategori_korban',
        'id_kategori_umur',
        'L',
        'P',
    ];

    // relasi ke kejadian
    public function kejadian()
    {
        return $this->belongsTo(Kejadian::class, 'id_kejadian');
    }

    // relasi ke kategori korban
    public function kategoriKorban()
    {
        return $this->belongsTo(KategoriKorban::class, 'id_kategori_korban');
    }

    // relasi ke kategori umur
    public function kategoriUmur()
    {
        return $this->belongsTo(KategoriUmur::class, 'id_kategori_umur');
    }
}