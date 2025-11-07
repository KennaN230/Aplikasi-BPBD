<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gelombang;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // ✅ import yang benar

class GelombangController extends Controller
{
    // 🟦 Menampilkan daftar data gelombang + filter
    public function index(Request $request)
    {
        $query = Gelombang::query();

        // === FILTER ===
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        // Ambil data hasil filter
        $gelombang = $query->orderBy('tanggal', 'asc')->paginate(10);

        // 🟧 Grafik harian
        $grafik = (clone $query)
            ->select('tanggal', 'tinggi_gelombang_max', 'tinggi_gelombang_min')
            ->orderBy('tanggal', 'asc')
            ->get();

        // 🟨 Rata-rata bulanan
        $rataBulan = (clone $query)
            ->selectRaw('
                MONTH(tanggal) as bulan,
                DATE_FORMAT(tanggal, "%M") as nama_bulan,
                AVG(tinggi_gelombang_max) as rata_max,
                AVG(tinggi_gelombang_min) as rata_min
            ')
            ->groupBy(DB::raw('MONTH(tanggal)'), DB::raw('DATE_FORMAT(tanggal, "%M")'))
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get();

        // 🟩 Ambil daftar tahun untuk dropdown
        $tahunList = Gelombang::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->pluck('tahun');

        return view('gelombang.index', [
            'gelombang' => $gelombang,
            'grafik' => $grafik,
            'rataBulan' => $rataBulan,
            'tahunList' => $tahunList,
        ]);
    }

    // 🟩 Form tambah data gelombang
    public function create()
    {
        return view('gelombang.create');
    }

    // 🟨 Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tinggi_gelombang_min' => 'required|numeric',
            'tinggi_gelombang_max' => 'required|numeric',
        ]);

        Gelombang::create($request->only(['tanggal', 'tinggi_gelombang_min', 'tinggi_gelombang_max']));

        return redirect()->route('gelombang.index')
            ->with('success', 'Data gelombang berhasil ditambahkan!');
    }

    // 🟦 Edit data
    public function edit($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        return view('gelombang.edit', compact('gelombang'));
    }

    // 🟧 Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tinggi_gelombang_min' => 'required|numeric',
            'tinggi_gelombang_max' => 'required|numeric',
        ]);

        $gelombang = Gelombang::findOrFail($id);
        $gelombang->update($request->only(['tanggal', 'tinggi_gelombang_min', 'tinggi_gelombang_max']));

        return redirect()->route('gelombang.index')
            ->with('success', 'Data gelombang berhasil diupdate!');
    }

    // 🟥 Hapus data
    public function destroy($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $gelombang->delete();

        return redirect()->route('gelombang.index')
            ->with('success', 'Data gelombang berhasil dihapus!');
    }

    private function getLogos()
    {
        $pathLogo1 = public_path('gambar/logo1.png');
        $pathLogo2 = public_path('gambar/logo2.png');

        $logo1 = file_exists($pathLogo1) ? base64_encode(file_get_contents($pathLogo1)) : null;
        $logo2 = file_exists($pathLogo2) ? base64_encode(file_get_contents($pathLogo2)) : null;

        return compact('logo1', 'logo2');
    }

    // 🟪 CETAK PDF TABEL
    public function gelombangpdf(Request $request)
    {
        $query = Gelombang::query();

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $gelombang = $query->orderBy('tanggal', 'asc')->get();
        $logos = $this->getLogos();

        $pdf = Pdf::loadView('gelombang.gelombangpdf', compact('gelombang'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('gelombang.pdf');
    }

    // 🟫 CETAK PDF GRAFIK
    public function pdfgrafik(Request $request)
    {
        $query = Gelombang::query();

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Grafik harian
        $grafik = (clone $query)
            ->select('tanggal', 'tinggi_gelombang_max', 'tinggi_gelombang_min')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Grafik bulanan
        $rataBulan = (clone $query)
            ->selectRaw('
                MONTH(tanggal) as bulan,
                DATE_FORMAT(tanggal, "%M") as nama_bulan,
                AVG(tinggi_gelombang_max) as rata_max,
                AVG(tinggi_gelombang_min) as rata_min
            ')
            ->groupBy(DB::raw('MONTH(tanggal)'), DB::raw('DATE_FORMAT(tanggal, "%M")'))
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get();

        // 🔹 Hapus dd() karena itu menghentikan proses PDF
        $pdf = Pdf::loadView('gelombang.pdfgrafik', compact('grafik', 'rataBulan'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('grafik_gelombang.pdf');
    }
}
