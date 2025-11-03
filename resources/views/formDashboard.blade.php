{{-- resources/views/formDashboard.blade.php --}}
@extends('layouts.app')
@section('title','Dashboard Admin')

@push('styles')
<style>
  :root{
    --navy:#0f2a4a; --cream:#efe8e0; --cream-hover:#e6ddd3;
    --tile-blue:#eef4ff; --tile-orange:#fff1e6;
    --accent:#ff7a00; --shadow:0 12px 28px rgba(0,0,0,.08);
    --radius:18px;
  }
  main.app-main{ padding-top:8px !important; }
  .dash-header{ margin:4px 0 12px; display:flex; align-items:center; gap:18px }
  .dash-header h2{font-weight:800;margin:0;color:#132d55}
  .dash-sub{color:#0B1C3F}
  .dash-user{display:flex;align-items:center;gap:12px}
  .dash-user .avatar{width:44px;height:44px;border-radius:50%;object-fit:cover}
  .btn-pill-sm{border-radius:999px;padding:.35rem .9rem;font-weight:700}
  .btn-icon{width:42px;height:42px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;padding:0}
  .btn-cream{ background:var(--cream)!important; border-color:var(--cream)!important; color:#0f2a4a; box-shadow:none!important; }
  .btn-cream:hover{ background:var(--cream-hover)!important; border-color:var(--cream-hover)!important; }
  .dropdown-cream.dropdown-menu{ background:var(--cream); border:0; border-radius:14px; box-shadow:var(--shadow); overflow:hidden; }
  .dropdown-cream .dm-head{ background:var(--cream); color:#0f2a4a; font-weight:700; padding:.6rem .9rem; border-bottom:1px solid var(--cream-hover) }
  .dropdown-cream .list-group-item{ background:transparent; border-color:var(--cream-hover) }

  .stats{border:0;border-radius:16px;background:#fff;box-shadow:var(--shadow)}
  .stats.primary{background:linear-gradient(180deg,var(--tile-blue),#fff)}
  .stats.warning{background:linear-gradient(180deg,var(--tile-orange),#fff)}
  .stats .title{font-weight:700}
  .stats .icon{width:42px;height:42px;border-radius:12px;background:#1a3a63;color:#fff;display:flex;align-items:center;justify-content:center}

  .progress.thin{height:10px;background:#e9edf5;border-radius:999px;overflow:hidden}
  .progress.thin .progress-bar{border-radius:999px}
  .progress.thin .progress-bar.blue{background:#1e4fa3}
  .progress.thin .progress-bar.orange{background:#f07a18}

  .toolbar{display:flex;align-items:center;gap:12px;margin:14px 0}
  .table-search{position:relative;max-width:360px}
  .table-search .bi-search{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#97a0b0}
  .table-search input{padding-left:40px;border-radius:12px}

  .soft-card{border:0;border-radius:16px;box-shadow:var(--shadow)}
  .table-wrap{max-height:460px;overflow:auto;border-radius:12px}
  .table-soft thead th{position:sticky;top:0;z-index:2;background:var(--navy);color:#fff;border:0!important}
  .table-soft tbody tr{border-color:#e9edf3}

  .badge.round{border-radius:999px;padding:.45rem .7rem;font-weight:700}
  .action-inline{display:flex;align-items:center;gap:10px;flex-wrap:nowrap;white-space:nowrap}
  .action-inline>*{flex:0 0 auto}
  .action-inline form{margin:0;display:inline-block}

  .btn-tambah{background:#EA620D;border-color:#EA620D;color:#fff}
  .btn-tambah:hover{background:#d45609;border-color:#d45609}
  .btn-hapus{background:#E30707;border-color:#E30707;color:#fff}
  .btn-hapus:hover{background:#C60A0A;border-color:#C60A0A}

  .table-wrap::-webkit-scrollbar{width:8px}
  .table-wrap::-webkit-scrollbar-thumb{background:#375078;border-radius:8px}

  .status-text{font-weight:700; text-align:center; color:#0f2a4a}
  .status-text.st-on{ color:#0b7f58; }
  .status-text.st-off{ color:#2b3851; }
</style>
@endpush

@section('content')
@php
  $me = auth()->user();
  $avatarUrl = ($me && $me->photo) ? asset('storage/'.$me->photo) : asset('gambar/profile.png');
  $avatarUrl .= '?t='.(optional($me->updated_at)->timestamp ?? time());

  $pendingUsers = ($pendingUsers ?? collect());
  $n = (int) ($pendingCount ?? $pendingUsers->count());

  /** @var array<int,\Carbon\Carbon|string> $lastActivityMap */
  $actMap  = $lastActivityMap ?? [];
  $window  = isset($sessionLifetime) ? (int)$sessionLifetime : 5; // menit
  $now     = now();
  $cutoff  = $now->copy()->subMinutes($window);

  $pickLast = function ($user) use ($actMap) {
      $raw = $actMap[(int)$user->id_user] ?? null;
      if (!$raw) return null;
      return $raw instanceof \Carbon\Carbon ? $raw : \Carbon\Carbon::parse($raw);
  };

  $isOnline = function ($user) use ($pickLast, $cutoff) {
      $last = $pickLast($user);
      return $last ? $last->gte($cutoff) : false;
  };

  $agoText = function ($user) use ($pickLast, $now) {
      $last = $pickLast($user);
      if (!$last) return 'belum ada aktivitas';
      $sec = $last->diffInSeconds($now);
      if ($sec < 60)    return 'baru saja';
      if ($sec < 3600)  return floor($sec/60).' menit yang lalu';
      if ($sec < 86400) return floor($sec/3600).' jam yang lalu';
      return floor($sec/86400).' hari yang lalu';
  };
@endphp

{{-- ===== HEADER ===== --}}
<div class="dash-header">
  <div>
    <h2>Selamat Datang!</h2>
    <div class="dash-sub">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
    <div class="dash-sub" id="clock">--:--:--</div>
  </div>

  <div class="ms-auto d-flex align-items-center gap:2">
    {{-- Notifikasi akun pending --}}
    <div class="dropdown notif me-1">
      <button class="btn btn-cream btn-icon position-relative" data-bs-toggle="dropdown" aria-label="Notifikasi">
        <i class="bi bi-bell"></i>
        @if($n > 0)
          <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle">{{ $n > 99 ? '99+' : $n }}</span>
        @endif
      </button>

      <div class="dropdown-menu dropdown-menu-end dropdown-cream p-0" style="min-width:320px">
        <div class="dm-head">Notifikasi</div>
        @if($pendingUsers->isEmpty())
          <div class="px-3 py-3 text-muted">Tidak ada akun pending.</div>
        @else
          <div class="list-group list-group-flush" style="max-height:320px;overflow:auto">
            @foreach($pendingUsers as $u)
              <div class="list-group-item">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <div class="fw-semibold">{{ $u->nama }}</div>
                    <div class="small text-muted">{{ $u->email }}</div>
                  </div>
                  <span class="badge text-bg-warning">Pending</span>
                </div>
                <div class="mt-2 d-flex gap-2">
                  @if (Route::has('users.approve'))
                  <form method="POST" action="{{ route('users.approve', $u->id_user) }}">@csrf
                    <button class="btn btn-sm btn-success"><i class="bi bi-check2 me-1"></i>Setujui</button>
                  </form>
                  @endif
                  @if (Route::has('users.reject'))
                  <form method="POST" action="{{ route('users.reject', $u->id_user) }}">@csrf
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                  </form>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Profil --}}
    <div class="dash-user">
      <img class="avatar" src="{{ $avatarUrl }}" alt="Foto {{ $me->nama ?? $me->name }}">
      <div>
        <div class="fw-semibold">{{ $me->nama ?? $me->name }}</div>
        <div class="small text-muted">{{ ucfirst(strtolower($me->role ?? 'User')) }}</div>
      </div>
      <div class="dropdown">
        <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="dropdown" aria-label="Menu profil">
          <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end dropdown-cream">
          @if (Route::has('profile.edit'))
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Edit Profil</a></li>
            <li><hr class="dropdown-divider" style="margin:.25rem 0;border-color:var(--cream-hover)"></li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="POST">@csrf
              <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@php
  $ta = (int) ($totalAdmin ?? 0);
  $tu = (int) ($totalUser  ?? 0);
  $total = max(1, $ta + $tu);
  $pAdmin = round(($ta / $total) * 100);
  $pUser  = round(($tu / $total) * 100);
@endphp

{{-- ===== KARTU STATISTIK ===== --}}
<div class="row g-3">
  <div class="col-md-6">
    <div class="card stats primary">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="title text-primary">Data Admin</div>
            <div class="display-6 fw-bold">{{ str_pad($ta,2,'0',STR_PAD_LEFT) }}</div>
          </div>
          <div class="icon"><i class="bi bi-person-gear"></i></div>
        </div>
        <div class="progress thin mt-3"><div class="progress-bar blue" style="width: {{ $pAdmin }}%"></div></div>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card stats warning">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="title" style="color:#b35c1a">Data User</div>
            <div class="display-6 fw-bold">{{ str_pad($tu,2,'0',STR_PAD_LEFT) }}</div>
          </div>
          <div class="icon" style="background:#b35c1a"><i class="bi bi-people"></i></div>
        </div>
        <div class="progress thin mt-3"><div class="progress-bar orange" style="width: {{ $pUser }}%"></div></div>
      </div>
    </div>
  </div>
</div>

{{-- ===== TOOLBAR (Search + Aksi) ===== --}}
<div class="toolbar">
  <form method="get" action="{{ route('dashboard') }}" class="table-search w-100" style="max-width:360px;">
    <i class="bi bi-search"></i>
    <input type="search" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Cari..">
  </form>
  <div class="ms-auto d-flex gap-2">
    {{-- CETAK LAPORAN HARIAN (modal pilih tanggal) --}}
    @if (Route::has('laporan.harian'))
      <button type="button" class="btn btn-cream btn-pill-sm" data-bs-toggle="modal" data-bs-target="#modalCetak">
        <i class="bi bi-printer me-1"></i> Cetak Laporan Harian
      </button>
    @endif

    {{-- Tambah user --}}
    @if (Route::has('users.store'))
    <button type="button" class="btn btn-tambah btn-pill-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-circle me-1"></i> Tambah
    </button>
    @endif

    {{-- Hapus banyak --}}
    <button id="btnDeleteSelected" type="button" class="btn btn-hapus btn-pill-sm">
      <i class="bi bi-trash me-1"></i> Hapus
    </button>
  </div>
</div>

{{-- FLASH MESSAGE --}}
@if(session('ok'))
  <div class="alert alert-success">{{ session('ok') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

{{-- ===== TABEL PENGGUNA ===== --}}
@php
  $list = $list ?? collect();
  $isPaginator = is_object($list) && method_exists($list,'links');
@endphp

<div class="card soft-card">
  <div class="table-wrap">
    <table class="table table-hover align-middle mb-0 table-soft">
      <thead>
      <tr>
        <th style="width:46px;"><input type="checkbox" id="checkAll"></th>
        <th style="width:70px;">No</th>
        <th>Nama Pengguna</th>
        <th>Email</th>
        <th style="width:120px;">Role</th>
        <th style="width:140px;">Approval</th>
        <th style="width:220px;">Status</th>
        <th style="width:220px;">Aksi</th>
      </tr>
      </thead>
      <tbody>
      @forelse($list as $u)
        @php
          $approval    = strtolower($u->status ?? 'pending');
          $online      = $isOnline($u);
          $statusText  = $online ? 'Aktif' : $agoText($u);
          $statusClass = $online ? 'st-on'  : 'st-off';
        @endphp
        <tr>
          <td><input type="checkbox" class="row-check" value="{{ $u->id_user }}"></td>
          <td>{{ ($isPaginator ? ($list->firstItem() ?? 1) : 1) + $loop->index }}</td>
          <td class="fw-semibold">{{ $u->nama }}</td>
          <td>{{ $u->email }}</td>

          <td>
            @if(strtolower($u->role) === 'admin')
              <span class="badge round text-bg-primary">Admin</span>
            @else
              <span class="badge round text-bg-info">User</span>
            @endif
          </td>

          <td>
            @if($approval === 'approved')
              <span class="badge round text-bg-success">Approved</span>
            @elseif($approval === 'rejected')
              <span class="badge round text-bg-danger">Rejected</span>
            @else
              <span class="badge round text-bg-warning">Pending</span>
            @endif
          </td>

          <td class="status-text {{ $statusClass }}">{{ $statusText }}</td>

          <td class="align-middle">
            <div class="action-inline">
              {{-- EDIT --}}
              <button type="button"
                      class="btn btn-warning btn-sm btn-pill-sm"
                      data-bs-toggle="modal" data-bs-target="#modalEdit"
                      data-id="{{ $u->id_user }}"
                      data-nama="{{ $u->nama }}"
                      data-email="{{ $u->email }}"
                      data-role="{{ $u->role }}"
                      data-approval="{{ $approval }}">
                <i class="bi bi-pencil-square me-1"></i> Edit
              </button>

              {{-- HAPUS --}}
              @if (Route::has('users.destroy'))
              <form method="post" action="{{ route('users.destroy', $u->id_user) }}"
                    onsubmit="return confirm('Hapus pengguna ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-hapus btn-sm btn-pill-sm" type="submit">
                  <i class="bi bi-trash me-1"></i> Hapus
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted">Belum ada data</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body py-2">{{ $isPaginator ? $list->links() : '' }}</div>
</div>

{{-- ===== MODAL CETAK LAPORAN HARIAN ===== --}}
@if (Route::has('laporan.harian'))
<div class="modal fade" id="modalCetak" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="GET" action="{{ route('laporan.harian') }}" target="_blank">
      <div class="modal-header">
        <h5 class="modal-title">Cetak Laporan Harian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Dari</label>
          <input type="date" name="from" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="mb-2">
          <label class="form-label">Sampai</label>
          <input type="date" name="to" class="form-control" value="{{ now()->toDateString() }}">
        </div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" value="1" id="stream" name="stream" checked>
          <label class="form-check-label" for="stream">Buka di tab (tidak langsung download)</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success"><i class="bi bi-check2 me-1"></i> Cetak</button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- ===== MODAL TAMBAH USER ===== --}}
@if (Route::has('users.store'))
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="{{ route('users.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nama</label><input name="nama" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="User">Pengguna</option>
              <option value="Admin">Admin</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Approval</label>
            <select name="status" class="form-select">
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Tambah</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

{{-- ===== MODAL EDIT USER ===== --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      {{-- action dinamis diisi via JS --}}
      <form method="post" id="formEdit">@csrf @method('PUT')
        <div class="modal-body">
          <div class="mb-3"><label class="form-label">Nama</label><input id="edit-nama" name="nama" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input id="edit-email" type="email" name="email" class="form-control" required></div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select id="edit-role" name="role" class="form-select" required>
              <option value="User">Pengguna</option>
              <option value="Admin">Admin</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Approval</label>
            <select id="edit-approval" name="status" class="form-select">
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i> Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Jam hidup
  const clockEl = document.getElementById('clock');
  if (clockEl) {
    const tick = () => {
      const d = new Date(), pad = n => String(n).padStart(2,'0');
      clockEl.textContent = `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    };
    tick(); setInterval(tick, 1000);
  }

  // Checklist all rows
  const checkAll = document.getElementById('checkAll');
  const rowChecks = () => Array.from(document.querySelectorAll('.row-check'));
  checkAll?.addEventListener('change', e => rowChecks().forEach(cb => cb.checked = e.target.checked));

  // Modal Edit: isi data & action
  const modalEdit = document.getElementById('modalEdit');
  modalEdit?.addEventListener('show.bs.modal', e => {
    const b = e.relatedTarget, id = b.getAttribute('data-id');
    document.getElementById('edit-nama').value     = b.getAttribute('data-nama');
    document.getElementById('edit-email').value    = b.getAttribute('data-email');
    document.getElementById('edit-role').value     = b.getAttribute('data-role');
    document.getElementById('edit-approval').value = b.getAttribute('data-approval');
    // set action form edit
    document.getElementById('formEdit').action     = `{{ url('/users') }}/${id}`;
  });

  // Hapus banyak
  document.getElementById('btnDeleteSelected')?.addEventListener('click', () => {
    const ids = rowChecks().filter(cb => cb.checked).map(cb => cb.value);
    if (ids.length === 0) return alert('Pilih minimal satu baris untuk dihapus.');
    if (!confirm(`Hapus ${ids.length} pengguna terpilih?`)) return;
    ids.forEach(id => {
      const f = document.createElement('form');
      f.method='POST'; f.action=`{{ url('/users') }}/${id}`;
      f.innerHTML = `@csrf @method('DELETE')`;
      document.body.appendChild(f); f.submit();
    });
  });
</script>
@endpush
