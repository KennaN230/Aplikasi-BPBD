<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ApiKejadianController extends Controller
{
    /** Ambil periode dari query (?from=YYYY-MM-DD&to=YYYY-MM-DD) */
    private function period(Request $r): array
    {
        $from = $r->query('from');
        $to   = $r->query('to');
        if (!$from || !$to) {
            $from = now()->copy()->startOfYear()->toDateString();
            $to   = now()->toDateString();
        }
        return [$from, $to];
    }

    /** Ambil id_kecamatan dari nama (case-insensitive). */
    private function kecId(string $name): ?int
    {
        if (!Schema::hasTable('tb_kecamatan')) return null;

        $row = DB::table('tb_kecamatan')
            ->whereRaw('LOWER(kecamatan) = ?', [mb_strtolower($name)])
            ->first();

        return $row ? (int)$row->id_kecamatan : null;
    }

    /** Resolusi id_nama_kejadian dari slug / nama / id. */
    private function kejadianNameToId(string $key): ?int
    {
        if (is_numeric($key)) return (int)$key;
        if (!Schema::hasTable('tb_nama_kejadian')) return null;

        $rows = DB::table('tb_nama_kejadian')
            ->select('id_nama_kejadian','nama_kejadian')
            ->get();

        foreach ($rows as $r) {
            if (Str::slug($r->nama_kejadian) === Str::slug($key)) {
                return (int)$r->id_nama_kejadian;
            }
        }
        return null;
    }

    /** GET /kejadian/daerah/{kecamatan} -> rangkuman jenis kejadian */
    public function summary(Request $r, string $kecamatan)
    {
        [$from, $to] = $this->period($r);
        $idKec = $this->kecId($kecamatan);

        if (!$idKec || !Schema::hasTable('tb_kejadian')) {
            return response()->json(['kecamatan'=>$kecamatan, 'rangkuman'=>[]]);
        }

        $q = DB::table('tb_kejadian as k')
            ->where('k.id_kecamatan', $idKec)
            ->whereBetween('k.tarikh', [$from, $to]);

        if (Schema::hasTable('tb_nama_kejadian')) {
            $q = $q->join('tb_nama_kejadian as n','n.id_nama_kejadian','=','k.id_nama_kejadian')
                   ->groupBy('n.id_nama_kejadian','n.nama_kejadian')
                   ->select(
                       'n.id_nama_kejadian as id',
                       'n.nama_kejadian as jenis',
                       DB::raw('COUNT(*) as jumlah')
                   );
        } else {
            $q = $q->groupBy('k.id_nama_kejadian')
                   ->select('k.id_nama_kejadian as id', DB::raw("CONCAT('Jenis #', k.id_nama_kejadian) as jenis"), DB::raw('COUNT(*) as jumlah'));
        }

        $items = $q->orderByDesc('jumlah')->get()->map(function($r){
            $label = (string) $r->jenis;
            return [
                'id'     => (int) $r->id,
                'slug'   => Str::slug($label),
                'jenis'  => $label,
                'jumlah' => (int) $r->jumlah,
            ];
        });

        return response()->json([
            'kecamatan' => $kecamatan,
            'rangkuman' => $items,
        ]);
    }

    /** GET /kejadian/daerah/{kecamatan}/{jenis} -> detail per jenis untuk tabs modal */
    public function detail(Request $r, string $kecamatan, string $jenis)
    {
        [$from, $to] = $this->period($r);
        $idKec = $this->kecId($kecamatan);
        $idNama = $this->kejadianNameToId($jenis);

        if (!$idKec || !Schema::hasTable('tb_kejadian')) {
            return response()->json([
                'lokasi'=>null,'korban'=>[],'kerusakan'=>[],'upaya'=>[]
            ]);
        }

        $base = DB::table('tb_kejadian as k')
            ->where('k.id_kecamatan', $idKec)
            ->whereBetween('k.tarikh', [$from, $to]);

        if ($idNama) $base->where('k.id_nama_kejadian', $idNama);

        $last = $base->orderByDesc('k.tarikh')->orderByDesc('k.waktu')
            ->first(['k.tarikh','k.waktu','k.kronologi','k.deskripsi']);

        $lokasi = [
            'alamat'   => $last->kronologi ?? ($last->deskripsi ?? "Kecamatan $kecamatan"),
            'kabupaten'=> 'Malang',
            'provinsi' => 'Jawa Timur',
            'foto_url' => null,
            'tanggal'  => $last ? trim(($last->tarikh ?? '').' '.($last->waktu ?? '')) : null,
        ];

        // Korban (opsional)
        $korban = ['meninggal'=>0,'luka'=>0,'hilang'=>0,'mengungsi'=>0,'L'=>0,'P'=>0,'total'=>0];
        if (Schema::hasTable('tb_korban') && Schema::hasTable('tb_kategori_korban')) {
            $q = DB::table('tb_korban as v')
                ->join('tb_kategori_korban as c','c.id_kategori_korban','=','v.id_kategori_korban')
                ->join('tb_kejadian as k','k.id_kejadian','=','v.id_kejadian')
                ->where('k.id_kecamatan',$idKec)
                ->whereBetween('k.tarikh', [$from, $to]);

            if ($idNama) $q->where('k.id_nama_kejadian', $idNama);

            $rows = $q->select('c.kategori_korban',
                    DB::raw('SUM(COALESCE(v.L,0)) as L'),
                    DB::raw('SUM(COALESCE(v.P,0)) as P'))
                ->groupBy('c.kategori_korban')->get();

            foreach ($rows as $r) {
                $L=(int)$r->L; $P=(int)$r->P; $sum=$L+$P;
                $name=strtolower($r->kategori_korban ?? '');
                if (str_contains($name,'meninggal'))   $korban['meninggal'] += $sum;
                elseif (str_contains($name,'luka'))     $korban['luka']      += $sum;
                elseif (str_contains($name,'hilang'))   $korban['hilang']    += $sum;
                elseif (str_contains($name,'mengungs')) $korban['mengungsi'] += $sum;
                $korban['L'] += $L; $korban['P'] += $P; $korban['total'] += $sum;
            }
        }

        // Kerusakan (opsional)
        $kerusakan = [
            'rumah'  => ['rb'=>0,'rs'=>0,'rr'=>0,'total'=>0],
            'sarpras'=> ['rb'=>0,'rs'=>0,'rr'=>0,'total'=>0],
        ];
        if (Schema::hasTable('tb_kerusakan_rumah')) {
            $q = DB::table('tb_kerusakan_rumah as r')
                ->join('tb_kejadian as k','k.id_kejadian','=','r.id_kejadian')
                ->where('k.id_kecamatan',$idKec)
                ->whereBetween('k.tarikh', [$from, $to]);
            if ($idNama) $q->where('k.id_nama_kejadian', $idNama);
            $s = $q->selectRaw('SUM(COALESCE(r.rmh_rb,0)) rb, SUM(COALESCE(r.rmh_rs,0)) rs, SUM(COALESCE(r.rmh_rr,0)) rr')->first();
            $kerusakan['rumah']['rb']=(int)($s->rb??0);
            $kerusakan['rumah']['rs']=(int)($s->rs??0);
            $kerusakan['rumah']['rr']=(int)($s->rr??0);
            $kerusakan['rumah']['total']=$kerusakan['rumah']['rb']+$kerusakan['rumah']['rs']+$kerusakan['rumah']['rr'];
        }
        if (Schema::hasTable('tb_kerusakan_sarpras')) {
            $q = DB::table('tb_kerusakan_sarpras as s')
                ->join('tb_kejadian as k','k.id_kejadian','=','s.id_kejadian')
                ->where('k.id_kecamatan',$idKec)
                ->whereBetween('k.tarikh', [$from, $to]);
            if ($idNama) $q->where('k.id_nama_kejadian', $idNama);
            $s = $q->selectRaw('SUM(COALESCE(s.rb,0)) rb, SUM(COALESCE(s.rs,0)) rs, SUM(COALESCE(s.rr,0)) rr')->first();
            $kerusakan['sarpras']['rb']=(int)($s->rb??0);
            $kerusakan['sarpras']['rs']=(int)($s->rs??0);
            $kerusakan['sarpras']['rr']=(int)($s->rr??0);
            $kerusakan['sarpras']['total']=$kerusakan['sarpras']['rb']+$kerusakan['sarpras']['rs']+$kerusakan['sarpras']['rr'];
        }

        return response()->json([
            'lokasi'    => $lokasi,
            'korban'    => $korban,
            'kerusakan' => $kerusakan,
            'upaya'     => [], // isi jika Anda punya tabel upaya
        ]);
    }
}