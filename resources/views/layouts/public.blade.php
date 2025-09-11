<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Halaman')</title>

  {{-- Bootstrap + Icon --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body{ background:#f2f5fb; color:#0b1c3f }
    .topbar{
      background:#EA620D; color:#fff; padding:10px 0; margin-bottom:14px;
      box-shadow:0 6px 18px rgba(0,0,0,.12);
    }
    .topbar .brand{ font-weight:800; letter-spacing:.2px }
    .container-narrow{ max-width: 1200px; }
    a, a:hover{ color:inherit }
  </style>

  @stack('styles')
</head>
<body>
  <header class="topbar">
    <div class="container container-narrow d-flex align-items-center justify-content-between">
      <div class="brand d-flex align-items-center gap-2">
        <img src="{{ asset('gambar/logo 1 1.png') }}" alt="Logo" width="32" onerror="this.style.display='none'">
        <span>BPBD Kabupaten Malang</span>
      </div>
      <nav class="d-flex align-items-center gap-3">
        <a class="text-white text-decoration-none" href="{{ route('home') }}"><i class="bi bi-house-door"></i> home</a>
        @auth
          <a class="text-white text-decoration-none" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        @endauth
      </nav>
    </div>
  </header>

  <main class="container container-narrow my-3">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
