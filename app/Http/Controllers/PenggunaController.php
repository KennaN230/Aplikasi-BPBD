<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        // Data dummy pengguna
        $user = [
            'nama' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => 'https://via.placeholder.com/80',
            'aktivitas' => 24,
            'titik_panas' => 5
        ];

        return view('pengguna.user', compact('user'));
    }
}
