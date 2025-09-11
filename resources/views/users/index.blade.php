@extends('layouts.app')
@section('title','Manajemen Pengguna')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Daftar Pengguna</h3>

    <form class="d-flex gap-2" method="get" action="{{ route('users.index') }}">
      <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Cari nama/email/role..">
      <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card soft-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:70px;">ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th style="width:140px;">Role</th>
            <th style="width:200px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>{{ $u->id_user }}</td>
              <td class="fw-semibold">{{ $u->nama }}</td>
              <td>{{ $u->email }}</td>
              <td>
                @if(strtolower($u->role) === 'admin')
                  <span class="badge rounded-pill text-bg-primary">Admin</span>
                @else
                  <span class="badge rounded-pill text-bg-secondary">User</span>
                @endif
              </td>
              <td class="text-nowrap">
                <div class="d-inline-flex gap-2">
                  <a href="{{ route('users.edit', $u->id_user) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </a>
                  <form method="post" action="{{ route('users.destroy', $u->id_user) }}" class="m-0"
                        onsubmit="return confirm('Hapus pengguna ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                      <i class="bi bi-trash me-1"></i> Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body py-2">
      {{ $users->links() }}
    </div>
  </div>
@endsection
