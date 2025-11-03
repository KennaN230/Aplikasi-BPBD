<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengawas extends Model
{
    use HasFactory;

    protected $table = 'tb_pengawas';   // nama tabel
    protected $primaryKey = 'nip_pengawas'; // primary key
    public $incrementing = false;       // karena NIP bukan auto increment
    protected $keyType = 'string';      // NIP biasanya string
    public $timestamps = false; // kalau tabel tidak punya created_at / updated_at

    protected $fillable = [
        'nip_pengawas',
        'nama_pengawas',
    ];

    // Relasi: satu pengawas bisa punya banyak kejadian
    public function kejadian()
    {
        return $this->hasMany(Kejadian::class, 'nip_pengawas', 'nip_pengawas');
    }
}