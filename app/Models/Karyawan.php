<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'tb_pengawas';
    protected $primaryKey = 'nip_pengawas';
    public $incrementing = false; // jika NIP bukan auto increment
    public $timestamps = false; // <== TAMBAHKAN INI

    protected $fillable = [
        'nip_pengawas',
        'nama_pengawas',
        'jabatan',
        'tugas'
    ];
}
