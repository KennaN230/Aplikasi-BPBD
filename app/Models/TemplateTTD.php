<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateTTD extends Model
{
    protected $table = 'template_ttd';
    protected $fillable = ['nip_pengawas','nama_pengawas','jabatan','jenis_template','is_active'];
}


