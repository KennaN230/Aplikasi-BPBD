<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Gempa;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TemplateTTD;

class GempaController extends Controller
{
    public function index()
    {
        $gempa = Gempa::orderBy('tanggal', 'desc')->get();

        $jumlahGempa = DB::table('gempa')
            ->selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah')
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('jumlah', 'bulan');

        $catatan = DB::table('gempa')
            ->selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah')
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('jumlah', 'bulan');

        $bulan = [
            "Januari","Februari","Maret","April","Mei","Juni",
            "Juli","Agustus","September","Oktober","November","Desember"
        ];

        $user = Auth::user();

        $srGempa = DB::table('gempa')
            ->select('sr', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('sr')
            ->orderBy('sr', 'asc')
            ->get();

        return view('gempa.index', compact('gempa','bulan','jumlahGempa','catatan','srGempa', 'user'));
    }

    public function create()
    {
        return view('gempa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'sr' => 'required|numeric',
            'waktu' => 'required',
            'gempa' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'keterangan' => 'required|string',
        ]);

        // ✅ tambahkan latitude & longitude ke data yang disimpan
        Gempa::create($request->only([
            'tanggal', 'sr', 'waktu', 'gempa', 'latitude', 'longitude', 'keterangan'
        ]));

        return redirect()->route('gempa.index')->with('success', 'Data gempa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $gempa = Gempa::findOrFail($id);
        return view('gempa.edit', compact('gempa'));
    }

    public function update(Request $request, $id)
    {
        $gempa = Gempa::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'sr' => 'required|numeric',
            'waktu' => 'required',
            'gempa' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'keterangan' => 'required|string',
        ]);

        // ✅ perbarui data dengan latitude & longitude
        $gempa->update([
            'tanggal' => $request->tanggal,
            'sr' => $request->sr,
            'waktu' => $request->waktu,
            'gempa' => $request->gempa,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('gempa.index')->with('success', 'Data gempa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $gempa = Gempa::findOrFail($id);
        $gempa->delete();

        return redirect()->route('gempa.index')->with('success', 'Data gempa berhasil dihapus.');
    }

    public function show($id)
    {
        $gempa = Gempa::findOrFail($id);
        return view('gempa.show', compact('gempa'));
    }

    // ✅ Cetak PDF
    public function cetakPdf(Request $request)
{
    $query = Gempa::query();
    $startDate = null;
    $endDate   = null;

    if ($request->tgl_mulai && $request->tgl_selesai) {
        $query->whereBetween('tanggal', [$request->tgl_mulai, $request->tgl_selesai]);
        $startDate = Carbon::parse($request->tgl_mulai)->translatedFormat('d F Y');
        $endDate   = Carbon::parse($request->tgl_selesai)->translatedFormat('d F Y');
    } elseif ($request->bulan && $request->tahun) {
        $query->whereMonth('tanggal', $request->bulan)
              ->whereYear('tanggal', $request->tahun);
        $startDate = Carbon::createFromDate($request->tahun, $request->bulan, 1)
                        ->translatedFormat('d F Y');
        $endDate   = Carbon::createFromDate($request->tahun, $request->bulan, 1)
                        ->endOfMonth()
                        ->translatedFormat('d F Y');
    } elseif ($request->tahun) {
        $query->whereYear('tanggal', $request->tahun);
        $startDate = 'Tahun ' . $request->tahun;
    } else {
        $startDate = Carbon::now()->translatedFormat('d F Y');
    }

    $kejadian = $query->orderBy('tanggal', 'desc')->get();

    // Logo
    $path1 = public_path('gambar/logo1.png');
    $path2 = public_path('gambar/logo2.png');

    $logo1 = file_exists($path1) ? base64_encode(file_get_contents($path1)) : null;
    $logo2 = file_exists($path2) ? base64_encode(file_get_contents($path2)) : null;

    $tanggal = $startDate . ($endDate ? ' s/d ' . $endDate : '');
    $tglTtd = Carbon::now()->translatedFormat('d F Y');

    // ==========================
    // AMBIL DATA TTD DARI DATABASE
    // ==========================
    $ttdPertama = TemplateTTD::where('is_active', 1)
        ->select('nama_pengawas', 'nip_pengawas', 'jabatan')
        ->orderBy('id', 'desc')
        ->first();

    $pdf = Pdf::loadView('gempa.pdf', compact(
        'kejadian', 
        'startDate', 
        'endDate', 
        'logo1', 
        'logo2', 
        'tanggal', 
        'tglTtd',
        'ttdPertama' // ← TAMBAHKAN INI
    ));

    return $pdf->stream('laporan-gempa.pdf');
}
}
