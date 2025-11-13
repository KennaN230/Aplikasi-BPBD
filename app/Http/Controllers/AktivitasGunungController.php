<?php

namespace App\Http\Controllers;

use App\Models\AktivitasGunung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\URL;

class AktivitasGunungController extends Controller
{
    public function index(Request $r)
    {
        $q = AktivitasGunung::query();

        if ($r->filled('q')) {
            $term = '%'.$r->q.'%';
            $q->where(function ($x) use ($term) {
                $x->where('gunung','like',$term)
                  ->orWhere('meteorologi','like',$term)
                  ->orWhere('visual','like',$term)
                  ->orWhere('aktivitas_vulkanik','like',$term)
                  ->orWhere('rekomendasi','like',$term);
            });
        }

        if ($r->filled('bulan')) $q->whereMonth('tanggal', (int) $r->bulan);
        if ($r->filled('tahun')) $q->whereYear('tanggal',  (int) $r->tahun);
        if ($r->filled('from'))  $q->whereDate('tanggal', '>=', $r->from);
        if ($r->filled('to'))    $q->whereDate('tanggal', '<=', $r->to);

        $items = $q->orderByDesc('tanggal')->paginate(15)->withQueryString();

        return view('aktivitas-gunung', compact('items'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'tanggal'            => ['required','date'],
            'gunung'             => ['nullable','string','max:150'],
            'meteorologi'        => ['nullable','string'],
            'visual'             => ['nullable','string'],
            'aktivitas_vulkanik' => ['nullable','string'],
            'rekomendasi'        => ['nullable','string'],
            'dokumentasi'        => ['nullable','file','max:20480'],
        ]);

        if ($r->hasFile('dokumentasi') && $r->file('dokumentasi')->isValid()) {
    $file = $r->file('dokumentasi');

    $ext = strtolower($file->getClientOriginalExtension());
    $isImg = in_array($ext, ['jpg','jpeg','png','webp']); // flag gambar

    $path = $file->store('aktivitas-gunung', 'public');

    $data['dokumentasi_path']    = $path;
    $data['dokumentasi_name']    = $file->getClientOriginalName();
    $data['dokumentasi_size']    = $file->getSize();
    $data['dokumentasi_is_image'] = $isImg; // cukup pakai ini
}

        AktivitasGunung::create($data);

        return redirect()->route('aktivitas-gunung.index')->with('ok', 'Data tersimpan.');
    }

    public function update(Request $r, AktivitasGunung $aktivitas_gunung)
    {
        $data = $r->validate([
    'tanggal'            => ['required','date'],
    'gunung'             => ['nullable','string','max:150'],
    'meteorologi'        => ['nullable','string'],
    'visual'             => ['nullable','string'],
    'aktivitas_vulkanik' => ['nullable','string'],
    'rekomendasi'        => ['nullable','string'],
    'dokumentasi'        => ['nullable','file','max:20480'], // hapus mimes
]);

if ($r->hasFile('dokumentasi') && $r->file('dokumentasi')->isValid()) {
    $file = $r->file('dokumentasi');

    $ext = strtolower($file->getClientOriginalExtension());
    $isImg = in_array($ext, ['jpg','jpeg','png','webp']); // flag gambar

    $path = $file->store('aktivitas-gunung', 'public');

    $data['dokumentasi_path']    = $path;
    $data['dokumentasi_name']    = $file->getClientOriginalName();
    $data['dokumentasi_size']    = $file->getSize();
    $data['dokumentasi_is_image'] = $isImg; // cukup pakai ini
}

        $aktivitas_gunung->update($data);

        return redirect()->route('aktivitas-gunung.index')->with('ok', 'Data diperbarui.');
    }

    public function destroy(AktivitasGunung $aktivitas_gunung)
    {
        if ($aktivitas_gunung->dokumentasi_path) {
            Storage::disk('public')->delete($aktivitas_gunung->dokumentasi_path);
        }
        $aktivitas_gunung->delete();

        return back()->with('ok', 'Data dihapus.');
    }

    public function exportXlsx(Request $r): StreamedResponse
    {
        $q = AktivitasGunung::query();

        if ($r->filled('q')) {
            $term = '%'.$r->q.'%';
            $q->where(function ($x) use ($term) {
                $x->where('gunung','like',$term)
                ->orWhere('meteorologi','like',$term)
                ->orWhere('visual','like',$term)
                ->orWhere('aktivitas_vulkanik','like',$term)
                ->orWhere('rekomendasi','like',$term);
            });
        }
        if ($r->filled('bulan')) $q->whereMonth('tanggal', (int) $r->bulan);
        if ($r->filled('tahun')) $q->whereYear('tanggal',  (int) $r->tahun);
        if ($r->filled('from'))  $q->whereDate('tanggal', '>=', $r->from);
        if ($r->filled('to'))    $q->whereDate('tanggal', '<=', $r->to);

        $rows = $q->orderByDesc('tanggal')->get();

        $ss = new Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $sheet->setTitle('Aktivitas Gunung');

        // Header
        $headers = ['Tanggal','Gunung','Meteorologi','Visual','Aktivitas Vulkanik','Rekomendasi','Dokumentasi (URL)'];
        $sheet->fromArray($headers, null, 'A1');

        // Data
        $rnum = 2;
        foreach ($rows as $it) {
            $docUrl = $it->dokumentasi_path ? URL::to('storage/'.$it->dokumentasi_path) : '';
            $sheet->fromArray([
                optional($it->tanggal)->format('Y-m-d'),
                (string)$it->gunung,
                (string)$it->meteorologi,
                (string)$it->visual,
                (string)$it->aktivitas_vulkanik,
                (string)$it->rekomendasi,
                $docUrl,
            ], null, "A{$rnum}");
            $rnum++;
        }

        // Styling ringan
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        foreach (range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($ss);
        $filename = 'aktivitas-gunung-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}