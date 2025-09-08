<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rain;

class RainController extends Controller
{
    // Menampilkan data + filter + grafik
    public function index(Request $request)
    {
        $bulan         = $request->get('bulan');
        $tahun         = $request->get('tahun');
        $cari          = $request->get('cari'); 
        $tanggal_awal  = $request->get('tanggal_awal');
        $tanggal_akhir = $request->get('tanggal_akhir');

        $query = Rain::query();

        // filter bulan
        if ($bulan) {
            $query->whereMonth('hari_tanggal', $bulan);
        }

        // filter tahun
        if ($tahun) {
            $query->whereYear('hari_tanggal', $tahun);
        }

        // filter kecamatan
        if ($cari) {
            $query->where('kecamatan', 'like', '%' . $cari . '%');
        }

        // filter rentang tanggal
        if ($tanggal_awal && $tanggal_akhir) {
            $query->whereBetween('hari_tanggal', [$tanggal_awal, $tanggal_akhir]);
        }

        // urutkan berdasarkan tanggal
        $data = $query->orderBy('hari_tanggal', 'asc')->get();

        // data grafik ikut hasil query
        $grafik = $data;

        return view('rain', compact('data', 'grafik', 'bulan', 'tahun', 'cari', 'tanggal_awal', 'tanggal_akhir'));
    }

    // Tampilkan form tambah data
    public function create()
    {
        return view('tambahrain'); 
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'hari_tanggal'   => 'required|date',
            'kecamatan'      => 'required|string|max:100',
            'hari_hujan'     => 'required|integer',
            'hari_tidak_hujan' => 'required|integer',
        ]);

        Rain::create($request->all());

        return redirect()->route('rain.index')->with('success', 'Data berhasil ditambahkan!');
    }

    // Edit data
    public function edit($id)
    {
        $rain = Rain::findOrFail($id);
        return view('rain_edit', compact('rain'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'hari_tanggal'   => 'required|date',
            'kecamatan'      => 'required|string|max:100',
            'hari_hujan'     => 'required|integer',
            'hari_tidak_hujan' => 'required|integer',
        ]);

        $rain = Rain::findOrFail($id);
        $rain->update($request->all());

        return redirect()->route('rain.index')->with('success', 'Data berhasil diupdate!');
    }

    // Hapus data
    public function destroy($id)
    {
        $rain = Rain::findOrFail($id);
        $rain->delete();

        return redirect()->route('rain.index')->with('success', 'Data berhasil dihapus!');
    }
}
