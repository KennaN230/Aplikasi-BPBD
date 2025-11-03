<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rain;
use Barryvdh\DomPDF\Facade\Pdf;

class RainController extends Controller
{
    // ✅ Tampilkan data & grafik
    public function index(Request $request)
    {
        $query = Rain::query();

        // Filter data
        if ($request->bulan) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }
        if ($request->cari) {
            $query->where('kecamatan', 'like', '%' . $request->cari . '%');
        }
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('hari_tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $data = $query->orderBy('hari_tanggal', 'asc')->get();

        return view('rain', [
            'data' => $data,
            'grafik' => $data,
        ]);
    }

    // ✅ Form tambah data
    public function create()
    {
        return view('tambahrain');
    }

    // ✅ Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'hari_tanggal'     => 'required|date',
            'kecamatan'        => 'required|string|max:100',
            'hari_hujan'       => 'required|integer',
            'hari_tidak_hujan' => 'required|integer',
        ]);

        Rain::create($request->all());

        return redirect()->route('rain.index')->with('success', 'Data berhasil ditambahkan!');
    }

    // ✅ Form edit data
    public function edit(Rain $rain)
    {
        return view('rain_edit', compact('rain'));
    }

    // ✅ Update data
    public function update(Request $request, Rain $rain)
    {
        $request->validate([
            'hari_tanggal'     => 'required|date',
            'kecamatan'        => 'required|string|max:100',
            'hari_hujan'       => 'required|integer',
            'hari_tidak_hujan' => 'required|integer',
        ]);

        $rain->update($request->all());

        return redirect()->route('rain.index')->with('success', 'Data berhasil diupdate!');
    }

    // ✅ Hapus data
    public function destroy(Rain $rain)
    {
        $rain->delete();

        return redirect()->route('rain.index')->with('success', 'Data berhasil dihapus!');
    }

    // ✅ Fungsi bantu untuk ambil logo dalam bentuk Base64
    private function getLogos()
    {
        $pathLogo1 = public_path('gambar/logo1.png');
        $pathLogo2 = public_path('gambar/logo2.png');

        $logo1 = file_exists($pathLogo1) ? base64_encode(file_get_contents($pathLogo1)) : null;
        $logo2 = file_exists($pathLogo2) ? base64_encode(file_get_contents($pathLogo2)) : null;

        return compact('logo1', 'logo2');
    }

    // ✅ Cetak PDF tabel data
    public function cetakPdf(Request $request)
    {
        $query = Rain::query();

        // Filter data
        if ($request->bulan) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }
        if ($request->cari) {
            $query->where('kecamatan', 'like', '%' . $request->cari . '%');
        }
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('hari_tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $data = $query->orderBy('hari_tanggal', 'asc')->get();

        // Ambil logo
        $logos = $this->getLogos();

        return view('rainpdf', array_merge($logos, [
            'kejadian' => $data,
            'tanggal' => $request->tanggal_awal && $request->tanggal_akhir
                ? $request->tanggal_awal . ' s/d ' . $request->tanggal_akhir
                : ($request->bulan ?? '') . ' ' . ($request->tahun ?? ''),
        ]));
    }

    // ✅ Cetak PDF Grafik
    public function cetakPdfGrafik(Request $request)
    {
        $query = Rain::query();

        if ($request->bulan) {
            $query->whereMonth('hari_tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('hari_tanggal', $request->tahun);
        }
        if ($request->cari) {
            $query->where('kecamatan', 'like', '%' . $request->cari . '%');
        }
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('hari_tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $data = $query->orderBy('hari_tanggal', 'asc')->get();

        // Ambil logo
        $logos = $this->getLogos();

        return view('rainpdfgrafik', array_merge($logos, [
            'kejadian' => $data,
            'tanggal' => $request->tanggal_awal && $request->tanggal_akhir
                ? $request->tanggal_awal . ' s/d ' . $request->tanggal_akhir
                : ($request->bulan ?? '') . ' ' . ($request->tahun ?? ''),
        ]));
    }
}