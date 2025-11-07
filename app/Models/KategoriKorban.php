<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKorban extends Model
{
    use HasFactory;

    protected $table = 'tb_kategori_korban'; // nama tabel di database
    protected $primaryKey = 'id_kategori_korban';         // sesuaikan dengan kolom PK
    public $timestamps = false;           // matikan kalau tidak pakai created_at & updated_at

    protected $fillable = [
        'nama_kategori',  // sesuaikan dengan kolom di tabel
    ];
}
