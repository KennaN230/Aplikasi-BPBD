<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengawas extends Model
{
    use HasFactory;

    protected $table = 'tb_pengawas';
    protected $primaryKey = 'nip_pengawas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nip_pengawas',
        'nama_pengawas',
    ];

    /**
     * Relasi many-to-many ke kejadian melalui tabel pivot kejadian_pengawas.
     */
    public function kejadian()
{
    return $this->hasMany(Kejadian::class, 'nip_pengawas', 'nip_pengawas');
}
}
