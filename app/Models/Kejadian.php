<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kejadian extends Model
{
    protected $table = 'tb_kejadian';
    protected $primaryKey = 'id_kejadian';
    public $timestamps = false;

    protected $fillable = [
    'id_jenis_bencana',
    'id_nama_kejadian',
    'tanggal',
    'waktu',
    'id_provinsi',
    'id_kabupaten',
    'id_kecamatan',
    'id_desa',
    'longitude',
    'latitude',
    'penyebab',
    'kronologi',
    'deskripsi',
    'sumber',
    'kondisi_mutakhir',
    'id_status_darurat',
    'upaya',
    'dokumentasi',
    'sebaran_dampak',
    'kib',
    'nip_pengawas'
];

    // Relasi ke tb_nama_kejadian
    public function namaKejadian()
    {
        return $this->belongsTo(NamaKejadian::class, 'id_nama_kejadian', 'id_nama_kejadian');
    }

    // Relasi ke tb_kecamatan
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    public function korban()
    {
        return $this->hasMany(Korban::class, 'id_kejadian', 'id_kejadian');
    }

    public function rumah()
{
    return $this->hasOne(Rumah::class, 'id_kejadian', 'id_kejadian');
}

// Relasi ke tb_jenis_bencana
public function jenisBencana()
{
    return $this->belongsTo(JenisBencana::class, 'id_jenis_bencana', 'id_jenis_bencana');
}

// Relasi ke tb_status_darurat
public function statusDarurat()
{
    return $this->belongsTo(StatusDarurat::class, 'id_status_darurat', 'id_status_darurat');
}

public function sosek()
{
    return $this->hasOne(Sosek::class, 'id_kejadian', 'id_kejadian');
}

public function sarpras()
{
    return $this->hasOne(Sarpras::class, 'id_kejadian', 'id_kejadian');
}

public function pelayanan()
{
    return $this->hasOne(Pelayanan::class, 'id_kejadian', 'id_kejadian');
}

public function pengawas()
{
    return $this->belongsTo(Pengawas::class, 'nip_pengawas', 'nip_pengawas');
}

}