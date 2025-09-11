@push('styles')
<style>
  .dash-header{ display:flex; align-items:center; gap:16px; margin-bottom:12px; }
  .dash-header h2{ font-weight:800; margin:0; }
  .dash-search-lg{ margin-left:auto; display:flex; align-items:center; gap:8px;
    background:#efe8e0; border-radius:999px; padding:8px 14px; min-width:260px; }
  .dash-user{ margin-left:12px; display:flex; align-items:center; gap:10px; }
  .dash-user .avatar{ width:40px; height:40px; border-radius:50%; object-fit:cover; }
  .progress{ height:8px; background:#eaeaea; border-radius:999px; }
  .table-wrap{ max-height:380px; overflow:auto; }
</style>
@endpush

@section('content')
  {{-- Header --}}
  <div class="dash-header">
    <div>
      <div class="text-muted fw-semibold">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
      <h2>Selamat Datang!</h2>
    </div>

    <div class="dash-search-lg">
      <i class="bi bi-search text-muted"></i>
      <input type="text" class="form-control border-0 bg-transparent" placeholder="Cari..">
    </div>

    <div class="dash-user">
      <img class="avatar" src="{{ asset('gambar/profile.png') }}" alt="me">
      <div>
        <div class="fw-semibold">{{ auth()->user()->nama ?? auth()->user()->name }}</div>
        <div class="small text-muted">{{ ucfirst(strtolower(auth()->user()->role)) }}</div>
      </div>
      <div class="dropdown">
        <button class="btn btn-light btn-round" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
        <ul class="dropdown-menu dropdown-menu-end">
          @if (Route::has('profile.edit'))
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Edit Profil</a></li>
            <li><hr class="dropdown-divider"></li>
          @endif
          <li>
            <form action="{{ route('logout') }}" method="POST">@csrf
              <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

