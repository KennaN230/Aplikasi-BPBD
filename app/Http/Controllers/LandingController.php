<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Kejadian;

class LandingController extends Controller
{
    private function parsePeriod(Request $r): array
    {
        $from = $r->filled('from') ? Carbon::parse($r->query('from'))->startOfDay() : now()->startOfMonth();
        $to   = $r->filled('to')   ? Carbon::parse($r->query('to'))->endOfDay()   : now();
        return [$from, $to];
    }

    private function sanitizeNum($v)
    {
        if (is_null($v)) return null;
        if (is_string($v)) $v = str_replace(',', '.', $v);
        $n = floatval($v);
        return is_finite($n) ? $n : null;
    }

    public function index(Request $r)
    {
        [$from, $to] = $this->parsePeriod($r);
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        $kejadian = Kejadian::with(['kecamatan', 'desa', 'jenisBencana', 'korban', 'rumah', 'sarpras'])
            ->whereBetween('tanggal', [$from->toDateString(), $to->toDateString()])
            ->get();

        $kejadianTotal = $kejadian->count();   

        // ========== BASE KEJADIAN ==========
        $kejBase = DB::table('tb_kejadian as j')
            ->join('tb_kecamatan as k','k.id_kecamatan','=','j.id_kecamatan')
            ->whereBetween('j.tanggal', [$fromDate, $toDate]);

        $kejadianTotal   = (clone $kejBase)->count('j.id_kejadian');
        $kecDisplayCount = (clone $kejBase)->distinct('k.id_kecamatan')->count('k.id_kecamatan');

        // === Titik per KECAMATAN (avg koordinat kejadian per kecamatan)
        $aggKec = (clone $kejBase)
            ->select(
                'k.id_kecamatan','k.kecamatan',
                DB::raw("AVG(NULLIF(NULLIF(REPLACE(j.latitude,  ',', '.'), ''), 0))  as lat"),
                DB::raw("AVG(NULLIF(NULLIF(REPLACE(j.longitude, ',', '.'), ''), 0)) as lng"),
                DB::raw('COUNT(j.id_kejadian) as cnt')
            )
            ->groupBy('k.id_kecamatan','k.kecamatan')
            ->get();

        $kecamatanPoints = collect($aggKec)->map(function ($row) {
            $lat = $this->sanitizeNum($row->lat);
            $lng = $this->sanitizeNum($row->lng);
            $valid = isset($lat,$lng) && $lat>=-90 && $lat<=90 && $lng>=-180 && $lng<=180;
            return (object)[
                'kecamatan'=> $row->kecamatan,
                'count'    => (int)$row->cnt,
                'lat'      => $valid ? $lat : null,
                'lng'      => $valid ? $lng : null,
            ];
        });

        $pointsKec         = $kecamatanPoints->whereNotNull('lat')->whereNotNull('lng')->values();
        $kecTanpaKoordinat = $kecamatanPoints->filter(fn($p)=>is_null($p->lat)||is_null($p->lng))
                                             ->pluck('kecamatan')->values();

        // === Titik per DESA (avg koordinat kejadian per desa)
        $aggDesa = (clone $kejBase)
            ->join('tb_desa as d','d.id_desa','=','j.id_desa')
            ->select(
                'd.id_desa','d.desa',
                'k.id_kecamatan','k.kecamatan',
                DB::raw("AVG(NULLIF(NULLIF(REPLACE(j.latitude,  ',', '.'), ''), 0))  as lat"),
                DB::raw("AVG(NULLIF(NULLIF(REPLACE(j.longitude, ',', '.'), ''), 0)) as lng"),
                DB::raw('COUNT(j.id_kejadian) as cnt')
            )
            ->groupBy('d.id_desa','d.desa','k.id_kecamatan','k.kecamatan')
            ->get();

        $desaPoints = collect($aggDesa)->map(function($row){
            $lat = $this->sanitizeNum($row->lat);
            $lng = $this->sanitizeNum($row->lng);
            $valid = isset($lat,$lng) && $lat>=-90 && $lat<=90 && $lng>=-180 && $lng<=180;
            return (object)[
                'desa'     => $row->desa,
                'kecamatan'=> $row->kecamatan,
                'count'    => (int)$row->cnt,
                'lat'      => $valid ? $lat : null,
                'lng'      => $valid ? $lng : null,
            ];
        })->whereNotNull('lat')->whereNotNull('lng')->values();

        // ========== HUJAN ==========
        $rainDateCol = Schema::hasColumn('rain','hari_tanggal') ? 'hari_tanggal'
                     : (Schema::hasColumn('rain','tanggal') ? 'tanggal' : null);

        $rainBase = DB::table('rain');
        if ($rainDateCol) $rainBase->whereBetween($rainDateCol, [$fromDate, $toDate]);

        $rainTotalHujan = (clone $rainBase)->sum('hari_hujan');
        $rainTotalTidak = (clone $rainBase)->sum('hari_tidak_hujan');
        $rainTop = (clone $rainBase)
            ->select('kecamatan', DB::raw('SUM(hari_hujan) as hujan'), DB::raw('SUM(hari_tidak_hujan) as tidak'))
            ->groupBy('kecamatan')->orderByDesc('hujan')->limit(10)->get();

        // ========== G E M P A ==========
        // Catatan: kita tidak mengandalkan kolom 'lokasi'/'keterangan' di DB.
        // Jika ada, akan dipakai; kalau tidak, kita fallback ke '-' agar blade tidak error.
        $gempaBase = DB::table('gempa as g');
        if (Schema::hasColumn('gempa','tanggal')) {
            $gempaBase->whereBetween('g.tanggal', [$fromDate, $toDate]);
        }

        // Hitung total gempa pada periode
        $gempaCount = (clone $gempaBase)->count('g.id');

        // List gempa untuk tabel kanan (maks. 10)
        $gempaListDB = (clone $gempaBase)
            ->select([
                'g.tanggal',
                'g.waktu',
                // magnitude (SR)
                Schema::hasColumn('gempa','sr') ? 'g.sr' : DB::raw('NULL as sr'),
                // lokasi/keterangan kalau ada kolomnya
                Schema::hasColumn('gempa','lokasi') ? 'g.lokasi' : DB::raw('NULL as lokasi'),
                Schema::hasColumn('gempa','keterangan') ? 'g.keterangan' : DB::raw('NULL as keterangan'),
                // lat/lng untuk dipakai di popup/fallback lokasi
                Schema::hasColumn('gempa','latitude')  ? 'g.latitude'  : DB::raw('NULL as latitude'),
                Schema::hasColumn('gempa','longitude') ? 'g.longitude' : DB::raw('NULL as longitude'),
            ])
            ->orderByDesc('g.tanggal')->orderByDesc('g.waktu')
            ->limit(10)
            ->get();

        $gempaList = $gempaListDB->map(function($r){
            $tanggal = $r->tanggal ? Carbon::parse($r->tanggal)->locale('id')->translatedFormat('d M Y') : '–';
            // waktu bisa tersimpan 'H:i:s' atau 'H:i' → coba keduanya
            $jam = '–';
            if (!empty($r->waktu)) {
                try { $jam = Carbon::createFromFormat('H:i:s',$r->waktu)->format('H:i'); }
                catch (\Exception $e) {
                    try { $jam = Carbon::createFromFormat('H:i',$r->waktu)->format('H:i'); }
                    catch (\Exception $e2) {}
                }
            }

            $lat = isset($r->latitude) ? (is_string($r->latitude) ? str_replace(',', '.', $r->latitude) : $r->latitude) : null;
            $lng = isset($r->longitude)? (is_string($r->longitude)? str_replace(',', '.', $r->longitude): $r->longitude): null;
            $latF = is_null($lat) ? null : floatval($lat);
            $lngF = is_null($lng) ? null : floatval($lng);

            // lokasi label: pakai kolom DB kalau ada; jika tidak ada, tampilkan "(lat, lng)" atau '-'
            $lokasiLabel = '-';
            if (!empty($r->lokasi)) {
                $lokasiLabel = $r->lokasi;
            } elseif (is_finite($latF) && is_finite($lngF)) {
                $lokasiLabel = $latF . ', ' . $lngF;
            }

            return [
                'tanggal'    => $tanggal,
                'jam'        => $jam,
                'mag'        => isset($r->sr) ? (is_null($r->sr) ? null : (float)$r->sr) : null,
                'lokasi'     => $lokasiLabel,
                // penting: selalu kirim kunci 'keterangan' agar blade tidak error
                'keterangan' => $r->keterangan ?? '-',
            ];
        });

        // Titik gempa untuk peta (ambil semua di periode dengan lat/lng valid)
        $gempaPointsDB = (clone $gempaBase)
            ->select([
                'g.id',
                Schema::hasColumn('gempa','latitude')  ? 'g.latitude'  : DB::raw('NULL as latitude'),
                Schema::hasColumn('gempa','longitude') ? 'g.longitude' : DB::raw('NULL as longitude'),
                Schema::hasColumn('gempa','sr')        ? 'g.sr'        : DB::raw('NULL as sr'),
                Schema::hasColumn('gempa','lokasi')    ? 'g.lokasi'    : DB::raw('NULL as lokasi'),
                'g.tanggal',
                'g.waktu',
            ])
            ->get();

        $gempaPoints = collect($gempaPointsDB)->map(function($r){
            $lat = is_null($r->latitude) ? null : (is_string($r->latitude) ? str_replace(',', '.', $r->latitude) : $r->latitude);
            $lng = is_null($r->longitude)? null : (is_string($r->longitude)? str_replace(',', '.', $r->longitude): $r->longitude);
            $latF = is_null($lat) ? null : floatval($lat);
            $lngF = is_null($lng) ? null : floatval($lng);
            $valid = isset($latF,$lngF) && is_finite($latF) && is_finite($lngF)
                     && $latF >= -90 && $latF <= 90 && $lngF >= -180 && $lngF <= 180;

            // label popup sederhana
            $tanggal = $r->tanggal ? Carbon::parse($r->tanggal)->format('d-m-Y') : '';
            $jam = $r->waktu ?? '';
            $label = trim(($r->lokasi ?? '') ?: (($valid) ? ($latF.', '.$lngF) : '' ));
            return [
                'id'        => $r->id,
                'latitude'  => $valid ? $latF : null,
                'longitude' => $valid ? $lngF : null,
                'sr'        => isset($r->sr) ? (is_null($r->sr) ? null : (float)$r->sr) : null,
                'label'     => $label !== '' ? $label : 'Gempa',
                'tanggal'   => $tanggal,
                'waktu'     => $jam,
            ];
        })->whereNotNull('latitude')->whereNotNull('longitude')->values();

        // ========== KERUSAKAN / KORBAN ==========
        $rumahAgg = (clone $kejBase)
            ->leftJoin('tb_kerusakan_rumah as rr','rr.id_kejadian','=','j.id_kejadian')
            ->selectRaw('COALESCE(SUM(rr.rmh_rb),0) rb, COALESCE(SUM(rr.rmh_rs),0) rs, COALESCE(SUM(rr.rmh_rr),0) rr, COALESCE(SUM(rr.kerugian),0) kerugian')
            ->first();
        $rumah = [
            'rb'=>(int)($rumahAgg->rb ?? 0),
            'rs'=>(int)($rumahAgg->rs ?? 0),
            'rr'=>(int)($rumahAgg->rr ?? 0),
            'total'=>(int)(($rumahAgg->rb ?? 0)+($rumahAgg->rs ?? 0)+($rumahAgg->rr ?? 0))
        ];
        $kerugian = (int)($rumahAgg->kerugian ?? 0);

        $spAgg = (clone $kejBase)
            ->leftJoin('tb_kerusakan_sarpras as sp','sp.id_kejadian','=','j.id_kejadian')
            ->selectRaw('COALESCE(SUM(sp.sarpras_rb),0) rb, COALESCE(SUM(sp.sarpras_rs),0) rs, COALESCE(SUM(sp.sarpras_rr),0) rr')
            ->first();
        $sarpras = [
            'rb'=>(int)($spAgg->rb ?? 0),
            'rs'=>(int)($spAgg->rs ?? 0),
            'rr'=>(int)($spAgg->rr ?? 0),
            'total'=>(int)(($spAgg->rb ?? 0)+($spAgg->rs ?? 0)+($spAgg->rr ?? 0))
        ];

        $korban = ['meninggal'=>0,'luka'=>0,'hilang'=>0,'mengungsi'=>0,'L'=>0,'P'=>0,'total'=>0];
        $kAgg = (clone $kejBase)
            ->leftJoin('tb_korban as kb','kb.id_kejadian','=','j.id_kejadian')
            ->leftJoin('tb_kategori_korban as kk','kk.id_kategori_korban','=','kb.id_kategori_korban')
            ->selectRaw("
                COALESCE(SUM(kb.L),0) as L,
                COALESCE(SUM(kb.P),0) as P,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban)='meninggal' THEN kb.L+kb.P ELSE 0 END),0) as meninggal,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban) IN ('luka','luka-luka') THEN kb.L+kb.P ELSE 0 END),0) as luka,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban) IN ('hilang','kehilangan') THEN kb.L+kb.P ELSE 0 END),0) as hilang,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban)='mengungsi' THEN kb.L+kb.P ELSE 0 END),0) as mengungsi
            ")->first();
        $korban['L']=(int)($kAgg->L??0); $korban['P']=(int)($kAgg->P??0);
        $korban['meninggal']=(int)($kAgg->meninggal??0); $korban['luka']=(int)($kAgg->luka??0);
        $korban['hilang']=(int)($kAgg->hilang??0); $korban['mengungsi']=(int)($kAgg->mengungsi??0);
        $korban['total']=$korban['L']+$korban['P'];

        // ========== CHART ==========
        $raw = DB::table('tb_kejadian')
            ->selectRaw("DATE_FORMAT(tanggal,'%Y-%m') as ym, COUNT(*) as c")
            ->whereBetween('tanggal', [$fromDate,$toDate])
            ->groupBy('ym')->pluck('c','ym');

        $period = CarbonPeriod::create($from->copy()->startOfMonth(), '1 month', $to->copy()->startOfMonth());
        $chartLabels = collect(); $chartCounts = collect();
        foreach ($period as $m) {
            $ym = $m->format('Y-m');
            $chartLabels->push($ym);
            $chartCounts->push((int)($raw[$ym] ?? 0));
        }

        return view('landing', [
            'from'=>$from, 'to'=>$to,

            'kejadianTotal'=>$kejadianTotal,
            'kecDisplayCount'=>$kecDisplayCount,

            'pointsKec'=>$pointsKec,
            'pointsDesa'=>$desaPoints,
            'kecTanpaKoordinat'=>$kecTanpaKoordinat,
            'countKec'=>$pointsKec->count(),
            'countDesa'=>$desaPoints->count(),

            'rainTotalHujan'=>$rainTotalHujan,
            'rainTotalTidak'=>$rainTotalTidak,
            'rainTop'=>$rainTop,

            'gempaCount'=>$gempaCount,
            'gempaList'=>$gempaList,     // sudah aman: punya 'lokasi' & 'keterangan'
            'gempaPoints'=>$gempaPoints, // dipakai oleh peta

            'rumah'=>$rumah,
            'sarpras'=>$sarpras,
            'kerugian'=>$kerugian,
            'korban'=>$korban,

            'chartLabels'=>$chartLabels,
            'chartCounts'=>$chartCounts,
        ]);
    }

    // ================== API kecamatan & desa (unchanged) ==================

    public function listKejadianDaerah(Request $r, string $kecamatan)
    {
        [$from, $to] = $this->parsePeriod($r);
        $kec = DB::table('tb_kecamatan')->where('kecamatan',$kecamatan)->first();
        if (!$kec) return response()->json(['rangkuman'=>[]]);
        $rows = DB::table('tb_kejadian as j')
            ->join('tb_jenis_bencana as b','b.id_jenis_bencana','=','j.id_jenis_bencana')
            ->where('j.id_kecamatan', $kec->id_kecamatan)
            ->whereBetween('j.tanggal', [$from->toDateString(),$to->toDateString()])
            ->select('b.id_jenis_bencana as id','b.jenis_bencana as jenis', DB::raw('COUNT(j.id_kejadian) as jumlah'))
            ->groupBy('b.id_jenis_bencana','b.jenis_bencana')
            ->orderByDesc('jumlah')
            ->get();
        return response()->json(['kecamatan'=>$kecamatan,'rangkuman'=>$rows]);
    }

    public function detailKejadianDaerah(Request $r, string $kecamatan, string $jenis)
    {
        [$from, $to] = $this->parsePeriod($r);
        $kec = DB::table('tb_kecamatan')->where('kecamatan',$kecamatan)->first();
        $jb  = DB::table('tb_jenis_bencana')->where('id_jenis_bencana',$jenis)->orWhere('jenis_bencana',$jenis)->first();
        if (!$kec || !$jb) return response()->json(['lokasi'=>[], 'korban'=>[], 'kerusakan'=>[], 'upaya'=>[]]);

        $kejIds = DB::table('tb_kejadian')
            ->where('id_kecamatan',$kec->id_kecamatan)
            ->where('id_jenis_bencana',$jb->id_jenis_bencana)
            ->whereBetween('tanggal', [$from->toDateString(),$to->toDateString()])
            ->pluck('id_kejadian');

        $last = DB::table('tb_kejadian as j')
            ->leftJoin('tb_desa as d','d.id_desa','=','j.id_desa')
            ->whereIn('j.id_kejadian', $kejIds)
            ->orderByDesc('j.tanggal')->orderByDesc('j.waktu')
            ->select('j.*','d.desa as nama_desa')
            ->first();

        $desaList = DB::table('tb_kejadian as j')
            ->join('tb_desa as d','d.id_desa','=','j.id_desa')
            ->whereIn('j.id_kejadian',$kejIds)
            ->groupBy('d.desa')->pluck('d.desa')->toArray();

        // --- Handle dokumentasi (bisa berupa JSON array atau string tunggal) ---
$rawDoc = $last->dokumentasi ?? null;

$doc = [];

// Jika tidak kosong, coba parse
if ($rawDoc) {
    $parsed = json_decode($rawDoc, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
        // Bentuk: ["img1.png", "img2.png"]
        $doc = $parsed;
    } else {
        // Bentuk: "dokumentasi/92wYgWLZ...png"
        $doc = [$rawDoc];
    }
}

$lokasi = [
    'desa'       => count($desaList) ? implode(', ', $desaList) : ($last->nama_desa ?? '-'),
    'kecamatan'  => $kecamatan,
    'kabupaten'  => 'Malang',
    'provinsi'   => 'Jawa Timur',
    'tanggal'    => $last ? Carbon::parse($last->tanggal)->format('d-m-Y') : '-',
    'dokumentasi'=> $doc  // selalu array
];

        $kAgg = DB::table('tb_korban as kb')
            ->leftJoin('tb_kategori_korban as kk','kk.id_kategori_korban','=','kb.id_kategori_korban')
            ->whereIn('kb.id_kejadian', $kejIds)
            ->selectRaw("
                COALESCE(SUM(kb.L),0) as L, COALESCE(SUM(kb.P),0) as P,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban)='meninggal' THEN kb.L+kb.P ELSE 0 END),0) as meninggal,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban) IN ('luka','luka-luka') THEN kb.L+kb.P ELSE 0 END),0) as luka,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban) IN ('hilang','kehilangan') THEN kb.L+kb.P ELSE 0 END),0) as hilang,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban)='mengungsi' THEN kb.L+kb.P ELSE 0 END),0) as mengungsi
            ")->first();

        $korban = [
            'meninggal'=>(int)($kAgg->meninggal ?? 0),
            'luka'=>(int)($kAgg->luka ?? 0),
            'hilang'=>(int)($kAgg->hilang ?? 0),
            'mengungsi'=>(int)($kAgg->mengungsi ?? 0),
            'L'=>(int)($kAgg->L ?? 0),'P'=>(int)($kAgg->P ?? 0),'total'=>(int)($kAgg->L ?? 0)+(int)($kAgg->P ?? 0),
        ];

        $rAgg = DB::table('tb_kerusakan_rumah')->whereIn('id_kejadian',$kejIds)->selectRaw('COALESCE(SUM(rmh_rb),0) rb, COALESCE(SUM(rmh_rs),0) rs, COALESCE(SUM(rmh_rr),0) rr')->first();
        $sAgg = DB::table('tb_kerusakan_sarpras')->whereIn('id_kejadian',$kejIds)->selectRaw('COALESCE(SUM(sarpras_rb),0) rb, COALESCE(SUM(sarpras_rs),0) rs, COALESCE(SUM(sarpras_rr),0) rr')->first();
        $kerusakan = ['rumah'=>['rb'=>(int)($rAgg->rb??0),'rs'=>(int)($rAgg->rs??0),'rr'=>(int)($rAgg->rr??0),'total'=>(int)(($rAgg->rb??0)+($rAgg->rs??0)+($rAgg->rr??0))],
                      'sarpras'=>['rb'=>(int)($sAgg->rb??0),'rs'=>(int)($sAgg->rs??0),'rr'=>(int)($sAgg->rr??0),'total'=>(int)(($sAgg->rb??0)+($sAgg->rs??0)+($sAgg->rr??0))] ];

        return response()->json(compact('lokasi','korban','kerusakan'));
    }

    public function listKejadianDesa(Request $r, string $kecamatan, string $desa)
    {
        [$from, $to] = $this->parsePeriod($r);
        $kec = DB::table('tb_kecamatan')->where('kecamatan',$kecamatan)->first();
        $ds  = DB::table('tb_desa')->where('desa',$desa)->first();
        if(!$kec || !$ds) return response()->json(['rangkuman'=>[]]);

        $rows = DB::table('tb_kejadian as j')
            ->join('tb_jenis_bencana as b','b.id_jenis_bencana','=','j.id_jenis_bencana')
            ->where('j.id_kecamatan',$kec->id_kecamatan)
            ->where('j.id_desa',$ds->id_desa)
            ->whereBetween('j.tanggal', [$from->toDateString(),$to->toDateString()])
            ->select('b.id_jenis_bencana as id','b.jenis_bencana as jenis', DB::raw('COUNT(j.id_kejadian) as jumlah'))
            ->groupBy('b.id_jenis_bencana','b.jenis_bencana')
            ->orderByDesc('jumlah')->get();

        return response()->json(['kecamatan'=>$kecamatan,'desa'=>$desa,'rangkuman'=>$rows]);
    }

    public function detailKejadianDesa(Request $r, string $kecamatan, string $desa, string $jenis)
    {
        [$from, $to] = $this->parsePeriod($r);
        $kec = DB::table('tb_kecamatan')->where('kecamatan',$kecamatan)->first();
        $ds  = DB::table('tb_desa')->where('desa',$desa)->first();
        $jb  = DB::table('tb_jenis_bencana')->where('id_jenis_bencana',$jenis)->orWhere('jenis_bencana',$jenis)->first();
        if (!$kec || !$ds || !$jb) return response()->json(['lokasi'=>[], 'korban'=>[], 'kerusakan'=>[]]);

        $kejIds = DB::table('tb_kejadian')
            ->where('id_kecamatan',$kec->id_kecamatan)->where('id_desa',$ds->id_desa)
            ->where('id_jenis_bencana',$jb->id_jenis_bencana)
            ->whereBetween('tanggal', [$from->toDateString(),$to->toDateString()])
            ->pluck('id_kejadian');

        $last = DB::table('tb_kejadian')->whereIn('id_kejadian',$kejIds)->orderByDesc('tanggal')->orderByDesc('waktu')->first();

        $lokasi = ['desa'=>$desa,'kecamatan'=>$kecamatan,'kabupaten'=>'Malang','provinsi'=>'Jawa Timur','tanggal'=>$last?Carbon::parse($last->tanggal)->format('d-m-Y'):'-','foto_url'=>null];

        $kAgg = DB::table('tb_korban as kb')->leftJoin('tb_kategori_korban as kk','kk.id_kategori_korban','=','kb.id_kategori_korban')
            ->whereIn('kb.id_kejadian',$kejIds)->selectRaw("
                COALESCE(SUM(kb.L),0) as L, COALESCE(SUM(kb.P),0) as P,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban)='meninggal' THEN kb.L+kb.P ELSE 0 END),0) as meninggal,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban) IN ('luka','luka-luka') THEN kb.L+kb.P ELSE 0 END),0) as luka,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban) IN ('hilang','kehilangan') THEN kb.L+kb.P ELSE 0 END),0) as hilang,
                COALESCE(SUM(CASE WHEN LOWER(kk.kategori_korban)='mengungsi' THEN kb.L+kb.P ELSE 0 END),0) as mengungsi
            ")->first();

        $korban = ['meninggal'=>(int)($kAgg->meninggal??0),'luka'=>(int)($kAgg->luka??0),'hilang'=>(int)($kAgg->hilang??0),'mengungsi'=>(int)($kAgg->mengungsi??0),'L'=>(int)($kAgg->L??0),'P'=>(int)($kAgg->P??0),'total'=>(int)($kAgg->L??0)+(int)($kAgg->P??0)];

        $rAgg = DB::table('tb_kerusakan_rumah')->whereIn('id_kejadian',$kejIds)->selectRaw('COALESCE(SUM(rmh_rb),0) rb, COALESCE(SUM(rmh_rs),0) rs, COALESCE(SUM(rmh_rr),0) rr')->first();
        $sAgg = DB::table('tb_kerusakan_sarpras')->whereIn('id_kejadian',$kejIds)->selectRaw('COALESCE(SUM(sarpras_rb),0) rb, COALESCE(SUM(sarpras_rs),0) rs, COALESCE(SUM(sarpras_rr),0) rr')->first();
        $kerusakan = ['rumah'=>['rb'=>(int)($rAgg->rb??0),'rs'=>(int)($rAgg->rs??0),'rr'=>(int)($rAgg->rr??0),'total'=>(int)(($rAgg->rb??0)+($rAgg->rs??0)+($rAgg->rr??0))],
                      'sarpras'=>['rb'=>(int)($sAgg->rb??0),'rs'=>(int)($sAgg->rs??0),'rr'=>(int)($sAgg->rr??0),'total'=>(int)(($sAgg->rb??0)+($sAgg->rs??0)+($sAgg->rr??0))] ];

        return response()->json(compact('lokasi','korban','kerusakan'));
    }
}