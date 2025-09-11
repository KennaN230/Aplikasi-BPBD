<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title','BPBD Kab. Malang')</title>

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    /* ============== TOKENS ============== */
    :root{
      --navy       : #0e2142;   /* sidebar */
      --navy-2     : #0f274a;   /* footer bar */
      --navy-3     : #142f5b;   /* header tabel */
      --cream      : #f1e9e0;   /* latar */
      --card       : #ffffff;
      --accent     : #ff7a00;   /* strip aktif sidebar */
      --blue-soft  : #e9efff;   /* panel “Data Admin” */
      --orange-soft: #f7e5d8;   /* panel “Data User” */
      --radius     : 18px;
      --sidebar-w  : 280px;     /* <<< samakan dgn lebar sidebar */
      --footer-h   : 35px;      /* tinggi footer */
    }

    *{ font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; }
    html,body{ height:100%; margin:0; }
    body{ background:var(--cream); }

    /* ============== APP GRID ============== */
    .app-shell{
      min-height:100vh;
      display:grid;
      grid-template-columns: var(--sidebar-w) 1fr; /* sidebar | konten */
      grid-template-rows: 1fr;                     /* konten saja (footer fixed) */
    }
    .app-sidebar{
      grid-column:1; grid-row:1;
      width:var(--sidebar-w);
    }
    .app-main{
      grid-column:2; grid-row:1;
      padding:24px;
      /* ruang utk footer fixed (+ safe area) */
      padding-bottom: calc(var(--footer-h) + 24px + env(safe-area-inset-bottom));
    }

    /* ============== SIDEBAR ============== */
    .sidebar{
      height:100%;
      background:var(--navy); color:#eaf1ff;
      padding:18px 14px;
      display:flex; flex-direction:column; gap:16px;
    }
    .brand{ background:#132a52; border-radius:16px; padding:16px 14px; }
    .brand h6{ font-weight:800; margin:0; line-height:1.15; }
    .brand .line{ height:3px; background:#dbe6ff1a; border-radius:999px; margin-top:12px; }

    .menu{ list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:8px; }
    .menu a{
      position:relative; display:flex; align-items:center; gap:12px;
      text-decoration:none; color:#eaf1ff; font-weight:700;
      padding:12px 14px; border-radius:14px; transition:all .18s ease;
    }
    .menu a .bi{ font-size:1.05rem; }
    .menu a:hover{ background:#0f2b55; color:#fff; }
    .menu a.is-active{
      background:var(--cream); color:#0f2444;
      box-shadow:inset 0 6px 18px rgba(0,0,0,.12);
    }
    .menu a.is-active::before{
      content:""; position:absolute; left:-12px; top:10px; bottom:10px;
      width:8px; background:var(--accent); border-radius:999px;
    }

    /* ============== TABEL (header navy) ============== */
    .table-navy thead th{
      background:var(--navy-3)!important; color:#fff; border:0;
      position:sticky; top:0; z-index:2;
    }
    .table-wrap{ max-height:430px; overflow:auto; border-radius:12px; }

    /* ============== PANEL ============== */
    .panel-blue, .panel-orange{
      background:var(--card);
      border-radius:16px;
      box-shadow:0 10px 25px rgba(0,0,0,.06);
    }
    .panel-blue .panel-head{
      background:var(--blue-soft); padding:12px 14px;
      border-top-left-radius:16px; border-top-right-radius:16px;
      font-weight:800; color:#233b7a;
    }
    .panel-orange .panel-head{
      background:var(--orange-soft); padding:12px 14px;
      border-top-left-radius:16px; border-top-right-radius:16px;
      font-weight:800; color:#9b4d12;
    }

    /* ============== FOOTER BAR (fixed & nyambung) ============== */
    .footer-bar{
      position:fixed;
      left:calc(var(--sidebar-w) + env(safe-area-inset-left));
      right:0;
      bottom:0;
      height:var(--footer-h);
      z-index:1050;

      background:var(--navy-2); color:#fff;
      display:flex; align-items:center; justify-content:flex-end;
      gap:5px; padding:0 18px;
      padding-bottom: max(0px, env(safe-area-inset-bottom));
      box-shadow:0 -6px 18px rgba(0,0,0,.06);
    }
    .footer-bar span{ white-space:nowrap; }
    .footer-bar .divider{ opacity:.5; }
    .footer-bar .social{ display:flex; gap:10px; }
    .footer-bar .social a{
      width:20px; height:20px;
      display:inline-flex; align-items:center; justify-content:center;
     color:#fff; border-radius:.5rem;
      text-decoration:none; line-height:1;
    }

    /* ============== RESPONSIVE ============== */
    @media (max-width: 992px){
      .app-shell{ grid-template-columns: 1fr; }
      .app-sidebar{ grid-row:auto; width:auto; }
      .footer-bar{ left:0; } /* kalau sidebar collapse, footer full width */
    }
    @media print{ .footer-bar{ display:none!important; } }
  </style>

  @stack('styles')
</head>
<body>
  <div class="app-shell">
    <!-- SIDEBAR -->
    <aside class="app-sidebar">
      @include('layouts.sidebar')
    </aside>

    <!-- KONTEN -->
    <main class="app-main">
      @if(session('ok'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
          {{ session('ok') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
          {{ $errors->first() }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <!-- FOOTER FIXED – menyatu dgn sidebar, kanan-bawah -->
  <div class="footer-bar" role="contentinfo" aria-label="Kontak BPBD Kabupaten Malang">
    <span><i class="bi bi-telephone me-1" aria-hidden="true"></i> +62 822 4409 4886</span>
    <span class="divider">|</span>
    <span>@bpbd_malangkab</span>
    <span class="divider">|</span>
    <span>BPBD KABUPATEN MALANG</span>
    <span class="social ms-2">
      <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
      <a href="#" aria-label="Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
      <a href="#" aria-label="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
      <a href="#" aria-label="X (Twitter)"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
    </span>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
