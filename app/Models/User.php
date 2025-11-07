<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'userr';
    protected $primaryKey = 'id_user';
    public $timestamps = true; // created_at & updated_at ada

    // kalau tabel tidak punya remember_token
    public function getRememberTokenName() { return null; }

    protected $fillable = [
        'nama','email','password','role','no_hp','photo',
        'status','approved_at','approved_by',
        'last_seen_at',               // <- TAMBAHKAN INI
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $casts = [
        'password'     => 'hashed',
        'approved_at'  => 'datetime',
        'last_seen_at' => 'datetime', // <- DAN INI
    ];

    public function approver()
    {
        return $this->belongsTo(self::class, 'approved_by', 'id_user');
    }

    public function isApproved(): bool
    {
        return strtolower((string) $this->status) === 'approved';
    }

    public function scopeApproved($q){ return $q->where('status','approved'); }
    public function scopePending($q){ return $q->where('status','pending'); }
}