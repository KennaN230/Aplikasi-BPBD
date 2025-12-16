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
        'id_nama_kejadian', // TAMBAHKAN INI
        'nama_kejadian', // TAMBAHKAN INI
        'tanggal',
        'waktu',
        'id_provinsi',
        'id_kabupaten',
        'id_kecamatan',
        'id_desa',
        'alamat',
        'longitude',
        'latitude',
        'penyebab',
        'kronologi',
        'deskripsi',
        'sumber',
        'logistik',
        'kondisi_mutakhir',
        'id_status_darurat',
        'upaya',
        'dokumentasi',
        'sebaran_dampak',
        'kib',
        'nip_pengawas',
        'unsur'
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

    // Relasi baru untuk MANY pengawas (semua NIP dalam string)
    public function semuaPengawas()
    {
        // Ambil NIP dari string yang dipisahkan koma
        $nips = $this->nip_pengawas ? explode(',', $this->nip_pengawas) : [];
        
        // Bersihkan spasi jika ada
        $nips = array_map('trim', $nips);
        
        // Query semua pengawas berdasarkan array NIP
        return Pengawas::whereIn('nip_pengawas', $nips)->get();
    }


    public function desa()
    {
        return $this->belongsTo(Desa::class, 'id_desa', 'id_desa');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'id_kabupaten', 'id_kabupaten');
    }

    // Accessor untuk memudahkan pengambilan pengawas sebagai array
    public function getPengawasArrayAttribute()
    {
        return $this->nip_pengawas ? explode(',', $this->nip_pengawas) : [];
    }
}