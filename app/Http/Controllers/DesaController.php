<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desa;

class DesaController extends Controller
{
    public function getDesa($id_kecamatan)
    {
        // Ambil semua desa yang memiliki id_kecamatan sesuai pilihan user
        $desa = Desa::where('id_kecamatan', $id_kecamatan)->get(['id_desa', 'desa', 'latitude', 'longitude']);
        return response()->json($desa);
    }
}
