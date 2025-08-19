<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Menentukan nama tabel yang digunakan
    protected $table = 'userr'; // Ganti 'users' dengan 'Userr'

    // Primary key
    protected $primaryKey = 'id_user'; // Pastikan sesuai dengan nama primary key di tabel

    // Jika tabel tidak memiliki created_at dan updated_at
    public $timestamps = false; // Atur ke false jika tabel tidak memiliki kolom created_at dan updated_at

    // Kolom yang dapat diisi (mass-assignable)
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'no_hp',
        'photo',
    ];

    // Kolom yang harus disembunyikan ketika serialisasi
    protected $hidden = [
        'password',
    ];

    // Enkripsi password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}