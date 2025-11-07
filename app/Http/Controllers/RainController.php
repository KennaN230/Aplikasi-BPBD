<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rain;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RainController extends Controller
{
    private function filterQuery(Request $request)
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

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->filterQuery($request);
        $data = $query->orderBy('hari_tanggal', 'asc')->get();

        $grafik = $this->filterQuery($request)
            ->select(
                'kecamatan',
                DB::raw('SUM(hari_hujan) as hari_hujan'),
                DB::raw('SUM(hari_tidak_hujan) as hari_tidak_hujan')
            )
            ->groupBy('kecamatan')
            ->orderBy('kecamatan', 'asc')
            ->get();

        return view('rain', [
            'data' => $data,
            'grafik' => $grafik,
        ]);
    }

    public function create()
    {
        return view('tambahrain');
    }

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

    public function edit(Rain $rain)
    {
        return view('rain_edit', compact('rain'));
    }

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

    public function destroy(Rain $rain)
    {
        $rain->delete();
        return redirect()->route('rain.index')->with('success', 'Data berhasil dihapus!');
    }

    private function getLogos()
    {
        $pathLogo1 = public_path('gambar/logo1.png');
        $pathLogo2 = public_path('gambar/logo2.png');

        $logo1 = file_exists($pathLogo1) ? base64_encode(file_get_contents($pathLogo1)) : null;
        $logo2 = file_exists($pathLogo2) ? base64_encode(file_get_contents($pathLogo2)) : null;

        return compact('logo1', 'logo2');
    }

    public function cetakPdf(Request $request)
    {
        $query = $this->filterQuery($request);
        $data = $query->orderBy('hari_tanggal', 'asc')->get();

        $logos = $this->getLogos();
        $tanggal = $this->getLabelTanggal($request);

        return view('rainpdf', array_merge($logos, [
            'kejadian' => $data,
            'tanggal'  => $tanggal,
        ]));
    }

    public function cetakPdfGrafik(Request $request)
    {
        $query = $this->filterQuery($request);

        $data = $query
            ->select(
                'kecamatan',
                DB::raw('SUM(hari_hujan) as total_hari_hujan'),
                DB::raw('SUM(hari_tidak_hujan) as total_hari_tidak_hujan')
            )
            ->groupBy('kecamatan')
            ->orderBy('kecamatan', 'asc')
            ->get();

        $logos = $this->getLogos();
        $tanggal = $this->getLabelTanggal($request);

        return view('rainpdfgrafik', array_merge($logos, [
            'kejadian' => $data,
            'tanggal'  => $tanggal,
        ]));
    }

    private function getLabelTanggal(Request $request)
    {
        if ($request->tanggal_awal && $request->tanggal_akhir) {
            return $request->tanggal_awal . ' s/d ' . $request->tanggal_akhir;
        }

        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $bulan = $request->bulan ? ($bulanNama[$request->bulan] ?? '') : '';
        $tahun = $request->tahun ?? '';

        return trim($bulan . ' ' . $tahun);
    }
}
