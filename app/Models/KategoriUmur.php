<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriUmur extends Model
{
    use HasFactory;

    protected $table = 'tb_kategori_umur'; // nama tabel
    protected $primaryKey = 'id';       // sesuaikan dengan PK tabel
    public $timestamps = false;         // matikan kalau tidak pakai created_at & updated_at

    protected $fillable = [
        'nama_kategori',  // sesuaikan dengan kolom tabel
    ];
}
