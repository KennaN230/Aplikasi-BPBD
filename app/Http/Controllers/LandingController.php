<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index(Request $r)
    {
        // Periode
        $from = $r->filled('from') ? Carbon::parse($r->query('from')) : now()->startOfYear();
        $to   = $r->filled('to')   ? Carbon::parse($r->query('to'))   : now();
        $from = $from->startOfDay();
        $to   = $to->endOfDay();

        $hasTable = fn(string $t) => Schema::hasTable($t);

        // Tabel sesuai ERD
        $T_KEJ  = $hasTable('tb_kejadian')   ? 'tb_kejadian'   : null;
        $T_KEC  = $hasTable('tb_kecamatan')  ? 'tb_kecamatan'  : null;
        $T_RAIN = $hasTable('rain')          ? 'rain'          : null;
        $T_EQ   = $hasTable('gempa')         ? 'gempa'         : null;

        $T_VIC  = $hasTable('tb_korban')             ? 'tb_korban'             : null;
        $T_VCAT = $hasTable('tb_kategori_korban')    ? 'tb_kategori_korban'    : null;
        $T_RMH  = $hasTable('tb_kerusakan_rumah')    ? 'tb_kerusakan_rumah'    : null;
        $T_SARP = $hasTable('tb_kerusakan_sarpras')  ? 'tb_kerusakan_sarpras'  : null;

        // ===== Kejadian total & titik peta =====
        $kejadianTotal = 0; $kecamatanPoints = collect(); $kecDisplayCount = 0; $chartLabels=[]; $chartCounts=[];

        if ($T_KEJ) {
            $kejadianTotal = DB::table($T_KEJ)
                ->whereBetween('tarikh', [$from->toDateString(), $to->toDateString()])
                ->count();

            $kecamatanPoints = DB::table($T_KEJ.' as k')
                ->leftJoin($T_KEC.' as c', 'c.id_kecamatan', '=', 'k.id_kecamatan')
                ->whereBetween('k.tarikh', [$from->toDateString(), $to->toDateString()])
                ->select(
                    'k.id_kecamatan',
                    DB::raw('COALESCE(c.kecamatan, CONCAT("Kec #", k.id_kecamatan)) as nama'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(CAST(NULLIF(k.latitude,  "") AS DECIMAL(10,6)))  as lat'),
                    DB::raw('AVG(CAST(NULLIF(k.longitude, "") AS DECIMAL(10,6)))  as lng')
                )
                ->groupBy('k.id_kecamatan', 'c.kecamatan')
                ->get()
                ->map(fn($r)=>[
                    'id'    => (int) $r->id_kecamatan,
                    'nama'  => (string) $r->nama,
                    'lat'   => is_null($r->lat)?null:(float)$r->lat,
                    'lng'   => is_null($r->lng)?null:(float)$r->lng,
                    'count' => (int) $r->count,
                ])
                ->filter(fn($p)=> isset($p['lat'],$p['lng']))
                ->values();

            $kecDisplayCount = $kecamatanPoints->where('count','>',0)->count();

            // Grafik per bulan (FIX: konversi DateTime -> Carbon sebelum locale)
            $rows = DB::table($T_KEJ)
                ->select(DB::raw("DATE_FORMAT(tarikh, '%Y-%m') as ym"), DB::raw('COUNT(*) as c'))
                ->whereBetween('tarikh', [$from->toDateString(), $to->toDateString()])
                ->groupBy('ym')->orderBy('ym')->get();

            $period = new \DatePeriod(
                $from->copy()->startOfMonth(),
                new \DateInterval('P1M'),
                $to->copy()->addMonth()->startOfMonth()
            );

            foreach ($period as $m) {
                $ym = $m->format('Y-m');
                // konversi ke Carbon agar bisa locale()
                $label = Carbon::instance($m)->locale('id')->translatedFormat('M Y');
                $chartLabels[] = $label;
                $chartCounts[] = (int) (collect($rows)->firstWhere('ym', $ym)->c ?? 0);
            }
        }

        // ===== Hujan =====
        $rainTotalHujan = 0; $rainTotalTidak = 0; $rainTop = collect();
        if ($T_RAIN) {
            $sum = DB::table($T_RAIN)
                ->select(DB::raw('SUM(hari_hujan) as sh'), DB::raw('SUM(hari_tidak_hujan) as st'))
                ->whereBetween('hari_tanggal', [$from->toDateString(), $to->toDateString()])
                ->first();
            $rainTotalHujan = (int)($sum->sh ?? 0);
            $rainTotalTidak = (int)($sum->st ?? 0);

            $rainTop = DB::table($T_RAIN)
                ->select('kecamatan',
                         DB::raw('SUM(hari_hujan) as hujan'),
                         DB::raw('SUM(hari_tidak_hujan) as tidak'))
                ->whereBetween('hari_tanggal', [$from->toDateString(), $to->toDateString()])
                ->groupBy('kecamatan')
                ->orderByDesc(DB::raw('SUM(hari_hujan)'))
                ->limit(10)->get()
                ->map(fn($r)=>['kecamatan'=>$r->kecamatan,'hujan'=>(int)$r->hujan,'tidak'=>(int)$r->tidak]);
        }

        // ===== Gempa =====
        $gempaCount = 0; $gempaList = collect();
        if ($T_EQ) {
            $q = DB::table($T_EQ)->whereBetween('tanggal', [$from->toDateString(), $to->toDateString()]);
            $gempaCount = (clone $q)->count();
            $gempaList = $q->orderByDesc('tanggal')->orderByDesc('waktu')
                ->limit(10)->get()->map(fn($g)=>[
                    'waktu'      => trim(($g->tanggal ?? '').' '.($g->waktu ?? '')),
                    'lokasi'     => (string)($g->gempa ?? '-'),
                    'mag'        => is_null($g->sr) ? null : (float)$g->sr,
                    'keterangan' => (string)($g->keterangan ?? ''),
                ]);
        }

        // ===== Dampak (opsional) =====
        $korban = ['meninggal'=>0,'luka'=>0,'hilang'=>0,'mengungsi'=>0,'total'=>0];
        if ($T_VIC && $T_VCAT && $T_KEJ) {
            $rows = DB::table($T_VIC.' as v')
                ->join($T_VCAT.' as c','c.id_kategori_korban','=','v.id_kategori_korban')
                ->join($T_KEJ.' as k','k.id_kejadian','=','v.id_kejadian')
                ->whereBetween('k.tarikh', [$from->toDateString(), $to->toDateString()])
                ->select('c.kategori_korban', DB::raw('SUM(COALESCE(v.L,0)+COALESCE(v.P,0)) as jml'))
                ->groupBy('c.kategori_korban')->get();

            foreach ($rows as $r) {
                $name = strtolower($r->kategori_korban ?? '');
                $val  = (int)$r->jml;
                if (str_contains($name,'meninggal'))   $korban['meninggal'] += $val;
                elseif (str_contains($name,'luka'))     $korban['luka']      += $val;
                elseif (str_contains($name,'hilang'))   $korban['hilang']    += $val;
                elseif (str_contains($name,'mengungs')) $korban['mengungsi'] += $val;
                $korban['total'] += $val;
            }
        }

        $rumah = ['rb'=>0,'rs'=>0,'rr'=>0,'total'=>0]; $sarpras = ['rb'=>0,'rs'=>0,'rr'=>0,'total'=>0]; $kerugian = 0;
        if ($T_RMH && $T_KEJ) {
            $s = DB::table($T_RMH.' as r')
                ->join($T_KEJ.' as k','k.id_kejadian','=','r.id_kejadian')
                ->whereBetween('k.tarikh', [$from->toDateString(), $to->toDateString()])
                ->select(
                    DB::raw('SUM(COALESCE(r.rmh_rb,0)) as rb'),
                    DB::raw('SUM(COALESCE(r.rmh_rs,0)) as rs'),
                    DB::raw('SUM(COALESCE(r.rmh_rr,0)) as rr'),
                    DB::raw('SUM(COALESCE(r.kerugian,0)) as rugi')
                )->first();
            $rumah['rb']=(int)($s->rb ?? 0); $rumah['rs']=(int)($s->rs ?? 0); $rumah['rr']=(int)($s->rr ?? 0);
            $rumah['total']=$rumah['rb']+$rumah['rs']+$rumah['rr']; $kerugian += (int)($s->rugi ?? 0);
        }
        if ($T_SARP && $T_KEJ) {
            $s = DB::table($T_SARP.' as s')
                ->join($T_KEJ.' as k','k.id_kejadian','=','s.id_kejadian')
                ->whereBetween('k.tarikh', [$from->toDateString(), $to->toDateString()])
                ->select(
                    DB::raw('SUM(COALESCE(s.rb,0)) as rb'),
                    DB::raw('SUM(COALESCE(s.rs,0)) as rs'),
                    DB::raw('SUM(COALESCE(s.rr,0)) as rr'),
                    DB::raw('SUM(COALESCE(s.taksiran,0)) as rugi')
                )->first();
            $sarpras['rb']=(int)($s->rb ?? 0); $sarpras['rs']=(int)($s->rs ?? 0); $sarpras['rr']=(int)($s->rr ?? 0);
            $sarpras['total']=$sarpras['rb']+$sarpras['rs']+$sarpras['rr']; $kerugian += (int)($s->rugi ?? 0);
        }

        return view('landing', compact(
            'from','to',
            'kejadianTotal','kecamatanPoints','kecDisplayCount','chartLabels','chartCounts',
            'rainTotalHujan','rainTotalTidak','rainTop',
            'gempaCount','gempaList',
            'korban','rumah','sarpras','kerugian'
        ));
    }
}
