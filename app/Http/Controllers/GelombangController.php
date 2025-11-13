<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gelombang;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as DompdfPdf;
use Carbon\Carbon;

class GelombangController extends Controller
{
    // Menampilkan daftar data gelombang + filter
    public function index(Request $request)
    {
        $query = Gelombang::query();

        if ($request->filled('bulan')) $query->whereMonth('tanggal', $request->bulan);
        if ($request->filled('tahun')) $query->whereYear('tanggal', $request->tahun);
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        $gelombang = $query->orderBy('tanggal', 'asc')->paginate(10);

        // Grafik harian
        $grafik = (clone $query)
            ->select('tanggal', 'tinggi_gelombang_max', 'tinggi_gelombang_min')
            ->orderBy('tanggal', 'asc')
            ->get();

        // Rata-rata bulanan
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

        $tahunList = Gelombang::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->pluck('tahun');

        return view('gelombang.index', compact('gelombang', 'grafik', 'rataBulan', 'tahunList'));
    }

    // Form tambah data
    public function create() { return view('gelombang.create'); }

    // Simpan data baru
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

    // Edit data
    public function edit($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        return view('gelombang.edit', compact('gelombang'));
    }

    // Update data
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

    // Hapus data
    public function destroy($id)
    {
        $gelombang = Gelombang::findOrFail($id);
        $gelombang->delete();

        return redirect()->route('gelombang.index')
            ->with('success', 'Data gelombang berhasil dihapus!');
    }

    // Ambil logo dalam base64
    private function getLogos()
{
    $pathLogo1 = public_path('gambar/logo1.png');
    $pathLogo2 = public_path('gambar/logo2.png');

    $logo1 = file_exists($pathLogo1) ? base64_encode(file_get_contents($pathLogo1)) : null;
    $logo2 = file_exists($pathLogo2) ? base64_encode(file_get_contents($pathLogo2)) : null;

    return compact('logo1', 'logo2');
}


    // Generate PDF tabel gelombang
    // Ambil query dengan filter
private function filterQuery(Request $request)
{
    $query = Gelombang::query();

    if ($request->filled('bulan')) $query->whereMonth('tanggal', $request->bulan);
    if ($request->filled('tahun')) $query->whereYear('tanggal', $request->tahun);
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
    }

    return $query;
}

// Ambil label periode
private function getLabelTanggal(Request $request)
{
    $query = $this->filterQuery($request)->orderBy('tanggal', 'asc')->get();

    return $query->isNotEmpty()
        ? Carbon::parse($query->first()->tanggal)->translatedFormat('d F Y') 
          . ' s/d ' . Carbon::parse($query->last()->tanggal)->translatedFormat('d F Y')
        : 'Semua Periode';
}

// Cetak PDF
public function cetakPdf(Request $request)
{
    $query = $this->filterQuery($request);
    $data = $query->orderBy('tanggal', 'asc')->get();

    $logos = $this->getLogos();
    $tanggal = $this->getLabelTanggal($request);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('gelombang.gelombangpdf', array_merge($logos, [
        'gelombang' => $data,
        'periode'   => $tanggal,
    ]))->setPaper('a4', 'portrait');

    // Jika query string ?download=1 dikirim, maka langsung download
    if ($request->query('download') == 1) {
        return $pdf->download('laporan_gelombang.pdf');
    }

    // Default: preview di browser
    return $pdf->stream('laporan_gelombang.pdf');
}

    // Generate PDF grafik gelombang
    public function pdfgrafik(Request $request)
{
    $query = Gelombang::query();
    if ($request->filled('bulan')) $query->whereMonth('tanggal', $request->bulan);
    if ($request->filled('tahun')) $query->whereYear('tanggal', $request->tahun);

    $grafik = (clone $query)
        ->select('tanggal', 'tinggi_gelombang_max', 'tinggi_gelombang_min')
        ->orderBy('tanggal', 'asc')
        ->get();

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

    // Periode
    if ($grafik->isNotEmpty()) {
        $periode = Carbon::parse($grafik->first()->tanggal)->translatedFormat('d F Y') .
                   ' s/d ' .
                   Carbon::parse($grafik->last()->tanggal)->translatedFormat('d F Y');
    } else {
        $periode = 'Semua Periode';
    }

    // Logo base64 (pastikan path benar)
    $logo1Path = public_path('gambar/logo1.png');
    $logo2Path = public_path('gambar/logo2.png');
    $logo1 = file_exists($logo1Path) ? base64_encode(file_get_contents($logo1Path)) : null;
    $logo2 = file_exists($logo2Path) ? base64_encode(file_get_contents($logo2Path)) : null;

    // Render view HTML dengan Chart.js
    return view('gelombang.pdfgrafik', compact('grafik', 'rataBulan', 'periode', 'logo1', 'logo2'));
}
}
