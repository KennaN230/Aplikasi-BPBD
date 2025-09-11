@extends('layouts.app')
@section('title','Edit Profile')

@push('styles')
<style>
  :root{ 
  --navy:#0f2a4a; 
  --peach:#efdfd2; 
  --ink:#2a3450; 
  --accent:#ff7a00; }
  .profile-wrap{
  max-width:5000px; 
  margin:50px auto;
}  
  .card-shell
  {background:#fff;
    border-radius:24px;
    padding:28px 32px;
    box-shadow:0 22px 50px rgba(0,0,0,.10);
  }
  .left-pane{
    background:#eff3f9;
    border-radius:20px;
    padding:56px 48px;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center
  }
  .avatar-wrap{
    position:relative
  }
  .avatar-lg{
    width:124px;
    height:124px;
    border-radius:50%;
    object-fit:cover;
    box-shadow:0 8px 22px rgba(0,0,0,.12)
  }
  .avatar-badge{
    position:absolute;
    right:6px;
    bottom:6px;
    width:30px;
    height:30px;
    border-radius:50%;
    background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;
    cursor:pointer
  }
  .btn-navy:hover{
    filter:brightness(1.06);
    color:#fff
  }
  .btn-navy-outline{
    border:2px solid var(--navy);
    color:var(--navy);
    background:#fff;
    border-radius:999px;
    font-weight:700
  }
  .btn-navy-outline:hover{
    background:var(--navy);
    color:#fff
  }
  .right-pane{
    background:var(--peach);
    border-radius:20px;
    padding:22px
  }
  .right-pane h5{
    font-weight:2100;
    color:var(--ink)
  }
  .right-pane .form-label{
    font-weight:1100;
    color:#32415f
  }
  .right-pane .form-control,.right-pane .form-select{
    background:#fff;
    border:0;
    border-radius:12px;
    box-shadow:inset 0 0 0 1px rgba(0,0,0,.07)
  }
  .btn-soft-danger{
    background:#fff;
    border:1px solid #dc3545;
    color:#dc3545;
    border-radius:999px;
    font-weight:700
  }
  .btn-soft-danger:hover{
    background:#dc3545;
    color:#fff
  }
  .btn-soft-success{
    background:#18a957;
    color:#fff;
    border-radius:999px;
    font-weight:700
  }
  .btn-soft-success:hover{
    filter:brightness(1.06);
    color:#fff
  }
  @media(max-width:991.98px){.left-pane{margin-bottom:12px}}
</style>
@endpush

@section('content')
<div class="profile-wrap">
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('ok') }} <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      {{ $errors->first() }} <button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card-shell">
    {{-- FORM membungkus kiri+kanan (agar file ikut terkirim) --}}
    <form id="formProfile" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf @method('PATCH')

      <div class="row g-4 align-items-start">
        {{-- LEFT --}}
        <div class="col-lg-4">
          <div class="left-pane text-center">
            <div class="avatar-wrap mb-2">
              <img id="avatarPreview"
                   src="{{ asset(($user->photo ? 'storage/'.$user->photo : 'gambar/profile.png')) }}"
                   class="avatar-lg" alt="avatar">
              <label class="avatar-badge" for="photoInput" title="Ganti foto">
                <i class="bi bi-camera-fill"></i>
              </label>
              <input id="photoInput" type="file" name="photo" class="d-none" accept="image/*">
            </div>
            <div class="fw-bold">{{ $user->nama ?? $user->name }}</div>
            <div class="text-muted small mb-3">{{ ucfirst(strtolower($user->role ?? 'User')) }}</div>

            <div class="d-grid gap-2 w-100">
              {{-- tidak disabled lagi, bisa diklik --}}
              <a href="{{ route('profile.edit') }}" class="btn btn-navy-outline">
                <i class="bi bi-person-gear me-1"></i> Edit Profile
              </a>
              <button type="button" class="btn btn-navy-outline" id="btnTogglePassword">
                <i class="bi bi-key me-1"></i> Edit Password
              </button>
            </div>
          </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-8">
          <div class="right-pane">
            <h5 class="mb-3">Edit Profile</h5>

            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input name="nama" class="form-control" value="{{ old('nama', $user->nama ?? $user->name) }}" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" value="{{ old('email',$user->email) }}" required>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Posisi</label>
                @php $roleNow = ucfirst(strtolower($user->role ?? 'User')); @endphp
                <select name="role" class="form-select" required>
                  <option value="User"  {{ $roleNow==='User'  ? 'selected' : '' }}>User</option>
                  <option value="Admin" {{ $roleNow==='Admin' ? 'selected' : '' }}>Admin</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <input class="form-control" value="Aktif" disabled>
              </div>
            </div>

            {{-- Password (opsional) --}}
            <div id="passwordFields" class="mt-3 d-none">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Password Baru</label>
                  <input name="password" type="password" class="form-control" minlength="6">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Konfirmasi Password</label>
                  <input name="password_confirmation" type="password" class="form-control" minlength="6">
                </div>
              </div>
              <div class="form-text">Kosongkan jika tidak ingin mengganti password.</div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
              <a href="{{ route('dashboard') }}" class="btn btn-soft-danger">
                <i class="bi bi-x-circle me-1"></i> Batalkan
              </a>
              <button type="submit" class="btn btn-soft-success">
                <i class="bi bi-check2-circle me-1"></i> Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Toggle field password
  document.getElementById('btnTogglePassword')?.addEventListener('click', () => {
    document.getElementById('passwordFields')?.classList.toggle('d-none');
  });

  // Preview foto
  const photoInput = document.getElementById('photoInput');
  const avatarPreview = document.getElementById('avatarPreview');
  photoInput?.addEventListener('change', e => {
    const file = e.target.files?.[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    avatarPreview.src = url;
  });
</script>
@endpush
