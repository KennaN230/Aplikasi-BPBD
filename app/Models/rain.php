<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rain extends Model
{
    use HasFactory;

    public $timestamps = false; // <-- ini WAJIB biar nggak insert created_at & updated_at

    protected $fillable = [
        'hari_tanggal',
        'kecamatan',
        'hari_hujan',
        'hari_tidak_hujan',
    ];
}
