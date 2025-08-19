<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rain extends Model
{
    use HasFactory;

    protected $fillable = [
        'kecamatan',
        'hari_hujan',
        'hari_tidak_hujan',
    ];
}
