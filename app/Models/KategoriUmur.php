<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriUmur extends Model
{
    protected $table = 'tb_kategori_umur';
    
    // Jika primary key bukan 'id', tambahkan ini
    protected $primaryKey = 'id_kategori_umur';
    
    // Jika tidak ingin menggunakan timestamps
    public $timestamps = false;
    
    protected $fillable = [
        'kategori_umur',
        // tambahkan kolom lain jika ada
    ];
}