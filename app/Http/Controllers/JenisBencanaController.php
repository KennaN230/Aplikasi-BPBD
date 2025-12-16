<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisBencana;
use App\Models\KlasifikasiBencana;

class JenisBencanaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jenis_bencana' => 'required',
            'id_klasifikasi_bencana' => 'required|exists:tb_klasifikasi_bencana,id_klasifikasi_bencana',
        ]);

        JenisBencana::create([
            'jenis_bencana' => $request->jenis_bencana,
            'id_klasifikasi_bencana' => $request->id_klasifikasi_bencana,
        ]);

        return back()->with('success', 'Jenis bencana berhasil ditambahkan!');
    }
}
