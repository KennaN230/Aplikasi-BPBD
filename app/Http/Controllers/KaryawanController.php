<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::all();
        return view('karyawan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip_pengawas' => 'required|unique:tb_pengawas,nip_pengawas',
            'nama_pengawas' => 'required',
            'jabatan' => 'required',
            'tugas' => 'required'
        ]);

        Karyawan::create($request->all());
        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function destroy($nip_pengawas)
    {
        Karyawan::findOrFail($nip_pengawas)->delete();
        return redirect()->back()->with('success', 'Karyawan berhasil dihapus!');
    }
}
