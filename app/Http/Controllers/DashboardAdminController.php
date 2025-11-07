<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index(Request $r)
    {
        // Query params
        $search  = trim((string) $r->query('q', ''));
        $perPage = max(1, min((int) $r->query('per_page', 10), 100));

        // Ringkasan role (pakai string SQL biasa, bukan DB::raw() yg diinterpolasi)
        $roleExpr   = 'LOWER(COALESCE(role, ""))';
        $totalAdmin = User::whereRaw("$roleExpr IN (?,?)", ['admin','administrator'])->count();
        $totalUser  = User::whereRaw("$roleExpr IN (?,?)", ['user','pengguna'])->count();

        // Ringkasan approval
        $countApproved = User::where('status','approved')->count();
        $countPending  = User::where('status','pending')->count();
        $countRejected = User::where('status','rejected')->count();

        // Daftar pengguna (tampilkan semua, bisa dicari)
        $list = User::when($search !== '', function ($q) use ($search) {
                    $q->where(function ($w) use ($search) {
                        $w->where('nama',  'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('role',  'like', "%{$search}%")
                          ->orWhere('status','like', "%{$search}%");
                    });
                })
                ->orderBy('id_user', 'asc')
                ->paginate($perPage)
                ->withQueryString();

        // ===== Status aktif / terakhir terlihat =====
        $onlineWindowMinutes = 5; // window aktif 5 menit

        // 1) last_seen_at dari users (dipastikan Carbon)
        $lastSeenDb = User::whereNotNull('last_seen_at')
            ->get(['id_user','last_seen_at'])
            ->mapWithKeys(fn ($u) => [
                (int)$u->id_user => ($u->last_seen_at instanceof Carbon)
                    ? $u->last_seen_at
                    : Carbon::parse($u->last_seen_at)
            ]);

        // 2) last_activity dari sessions (unix ts)
        $lastSeenSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('MAX(last_activity) as la'))
            ->groupBy('user_id')
            ->pluck('la', 'user_id')
            ->map(fn ($ts) => Carbon::createFromTimestamp((int) $ts));

        // 3) Gabungkan → ambil yang paling baru
        $lastActivityMap = $lastSeenDb->toArray();
        foreach ($lastSeenSessions as $uid => $carbonTs) {
            if (!isset($lastActivityMap[$uid]) || $carbonTs->gt($lastActivityMap[$uid])) {
                $lastActivityMap[$uid] = $carbonTs;
            }
        }

        // Notifikasi pending
        $pendingUsers = User::where('status', 'pending')
            ->orderByRaw('COALESCE(created_at, id_user) asc')
            ->get();

        return view('formDashboard', [
            'totalAdmin'      => $totalAdmin,
            'totalUser'       => $totalUser,
            'countApproved'   => $countApproved,
            'countPending'    => $countPending,
            'countRejected'   => $countRejected,
            'list'            => $list,
            'search'          => $search,
            'pendingUsers'    => $pendingUsers,
            'pendingCount'    => $pendingUsers->count(),
            'lastActivityMap' => $lastActivityMap,      // [id_user => Carbon]
            'sessionLifetime' => $onlineWindowMinutes,  // menit
        ]);
    }
}