<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusDarurat extends Model
{
    use HasFactory;

    protected $table = 'tb_status_darurat'; // nama tabel
    protected $primaryKey = 'id_status_darurat'; // primary key custom

    protected $fillable = [
        'status'
    ];

    public $timestamps = false;

    // Relasi ke Kejadian (1 StatusDarurat bisa dipakai banyak Kejadian)
    public function kejadian()
    {
        return $this->hasMany(Kejadian::class, 'id_status_darurat', 'id_status_darurat');
    }
}
