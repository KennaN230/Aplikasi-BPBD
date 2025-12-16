<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriKorban extends Model
{
    protected $table = 'tb_kategori_korban';
    
    // Jika primary key bukan 'id'
    protected $primaryKey = 'id_kategori_korban';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kategori_korban',
    ];
}