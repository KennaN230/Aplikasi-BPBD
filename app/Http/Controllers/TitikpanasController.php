<?php

namespace App\Http\Controllers;

use App\Models\TitikPanas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TitikpanasController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $bulan  = $request->integer('bulan');
        $tahun  = $request->integer('tahun');
        $from   = $request->query('from');
        $to     = $request->query('to');

        $query = TitikPanas::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kecamatan', 'like', "%{$q}%")
                  ->orWhere('keterangan', 'like', "%{$q}%")
                  ->orWhere('satelit', 'like', "%{$q}%");
            });
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($from && $to) {
            $query->whereBetween('tanggal', [$from, $to]);
        } elseif ($from) {
            $query->whereDate('tanggal', '>=', $from);
        } elseif ($to) {
            $query->whereDate('tanggal', '<=', $to);
        }

        $items = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        return view('titikpanas.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal'     => ['required','date'],
            'titik_panas' => ['required','integer','min:0'],
            'latitude'    => ['required','string','max:50'],
            'longitude'   => ['required','string','max:50'],
            'kecamatan'   => ['required','string','max:120'],
            'satelit'     => ['nullable','string','max:120'],
            'waktu'       => ['nullable','date_format:H:i'],
            'keterangan'  => ['nullable','string','max:255'],
        ]);

        $row = TitikPanas::create($data);

        if ($request->expectsJson() || $request->header('Content-Type') === 'application/json') {
            return response()->json(['status' => 'success', 'data' => $row], 201);
        }

        return redirect()->route('titikpanas.index')->with('ok', 'Data titik panas berhasil disimpan.');
    }

    public function update(Request $request, TitikPanas $titikpanas)
    {
        $data = $request->validate([
            'tanggal'     => ['required','date'],
            'titik_panas' => ['required','integer','min:0'],
            'latitude'    => ['required','string','max:50'],
            'longitude'   => ['required','string','max:50'],
            'kecamatan'   => ['required','string','max:120'],
            'satelit'     => ['nullable','string','max:120'],
            'waktu'       => ['nullable','date_format:H:i'],
            'keterangan'  => ['nullable','string','max:255'],
        ]);

        $titikpanas->update($data);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $titikpanas]);
        }

        return redirect()->route('titikpanas.index')->with('ok', 'Data titik panas berhasil diperbarui.');
    }

    public function destroy(TitikPanas $titikpanas)
    {
        $titikpanas->delete();
        return redirect()->route('titikpanas.index')->with('ok', 'Data titik panas berhasil dihapus.');
    }

    public function cetakPdf(Request $request)
    {
        // Filter sama seperti index()
        $q      = trim((string) $request->query('q', ''));
        $bulan  = $request->integer('bulan');
        $tahun  = $request->integer('tahun');
        $from   = $request->query('from') ?? $request->query('tgl_mulai');
        $to     = $request->query('to')   ?? $request->query('tgl_selesai');

        $query = TitikPanas::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kecamatan', 'like', "%{$q}%")
                  ->orWhere('keterangan', 'like', "%{$q}%")
                  ->orWhere('satelit', 'like', "%{$q}%");
            });
        }
        if ($bulan) { $query->whereMonth('tanggal', $bulan); }
        if ($tahun) { $query->whereYear('tanggal', $tahun); }

        $startDate = null; $endDate = null;

        if ($from && $to) {
            $query->whereBetween('tanggal', [$from, $to]);
            $startDate = Carbon::parse($from)->translatedFormat('d F Y');
            $endDate   = Carbon::parse($to)->translatedFormat('d F Y');
        } elseif ($bulan && $tahun) {
            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('d F Y');
            $endDate   = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->translatedFormat('d F Y');
        } elseif ($tahun) {
            $startDate = 'Tahun '.$tahun;
        } else {
            $startDate = Carbon::now()->translatedFormat('d F Y');
        }

        $items    = $query->orderBy('tanggal', 'desc')->get();
        $kejadian = $items; // kompatibel dengan view lama

        // Logo opsional
        $path1 = public_path('gambar/logo1.png');
        $path2 = public_path('gambar/logo2.png');
        $logo1 = file_exists($path1) ? base64_encode(file_get_contents($path1)) : null;
        $logo2 = file_exists($path2) ? base64_encode(file_get_contents($path2)) : null;

        $tanggal = $startDate . ($endDate ? ' s/d ' . $endDate : '');

        $pdf = Pdf::loadView('titikpanas.pdf', compact('items','kejadian','startDate','endDate','logo1','logo2','tanggal'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-titikpanas.pdf');
    }
}
