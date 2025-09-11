<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:Admin']);
    }

    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q',''));
        $filter = strtolower((string) $request->query('status',''));

        // Default: tampilkan semua KECUALI rejected
        $list = User::query()
            ->when($filter === 'pending',  fn($w)=>$w->where('status','pending'))
            ->when($filter === 'approved', fn($w)=>$w->where('status','approved'))
            ->when($filter === 'rejected', fn($w)=>$w->where('status','rejected'))     // dipakai jika admin mau melihat yang ditolak
            ->when(!in_array($filter,['pending','approved','rejected']), fn($w)=>$w->where('status','!=','rejected'))
            ->when($q !== '', function($w) use ($q){
                $w->where(function($x) use ($q){
                    $x->where('nama','like',"%$q%")
                      ->orWhere('email','like',"%$q%")
                      ->orWhere('role','like',"%$q%");
                });
            })
            ->orderBy('id_user')
            ->paginate(10)->withQueryString();

        // kirim juga data untuk header/summary di dashboard
        $pendingUsers = User::where('status','pending')->orderBy('id_user')->take(10)->get();
        $pendingCount = User::where('status','pending')->count();
        $roleCol = 'LOWER(COALESCE(role,""))';
        $totalAdmin = User::where('status','approved')->whereRaw("$roleCol IN (?,?)",['admin','administrator'])->count();
        $totalUser  = User::where('status','approved')->whereRaw("$roleCol IN (?,?)",['user','pengguna'])->count();

        return view('formDashboard', [
            'list'         => $list,
            'search'       => $q,
            'pendingUsers' => $pendingUsers,
            'pendingCount' => $pendingCount,
            'totalAdmin'   => $totalAdmin,
            'totalUser'    => $totalUser,
            'onlineUserIds'=> [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => ['required','string','max:100'],
            'email'    => ['required','email','unique:userr,email'],
            'password' => ['required','min:6'],
            'role'     => ['required','in:User,Admin'],
        ]);

        $data['password']    = Hash::make($data['password']);
        $data['status']      = 'approved';
        $data['approved_at'] = now();
        $data['approved_by'] = auth()->id();

        User::create($data);
        return back()->with('ok','Pengguna ditambahkan (langsung approved).');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'nama'     => ['required','string','max:100'],
            'email'    => ['required','email', Rule::unique('userr','email')->ignore($user->id_user,'id_user')],
            'role'     => ['required','in:User,Admin'],
            'password' => ['nullable','min:6'],
        ]);

        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);

        $user->update($data);
        return back()->with('ok','Pengguna diperbarui.');
    }

    public function destroy($id)
    {
        User::where('id_user',$id)->delete();
        return back()->with('ok','Pengguna dihapus.');
    }

    public function approve($id)
    {
        $u = User::findOrFail($id);
        $u->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);
        return back()->with('ok','Akun disetujui.');
    }

    public function reject($id)
    {
        $u = User::findOrFail($id);
        $u->update([
            'status'      => 'rejected',
            'approved_at' => null,
            'approved_by' => null,
        ]);
        return back()->with('ok','Akun ditolak (disembunyikan dari tabel).');
    }
}
