<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Halaman')</title>

  {{-- Fonts + Bootstrap + Icons --}}
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root{
      --ink:#0f172a; --muted:#64748b; --border:#eef2f7;
      --orange:#EA620D; --orange-dark:#cf580c;
      --container:1200px;
    }
    html,body{font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial}
    body{ background:#f2f5fb; color:var(--ink) }

    .container-narrow{max-width: var(--container)}

    /* ===== NAVBAR ORANYE ===== */
    .nav-orange{
      position: sticky; top: 0; z-index: 1100;
      background: var(--orange);
      border-bottom: 0;
      box-shadow: 0 8px 22px rgba(15,23,42,.12);
      color:#fff;
    }
    .nav-inner{
      max-width: var(--container); margin:0 auto; padding:10px 16px;
      display:flex; align-items:center; gap:14px;
    }
    .brand{display:flex; align-items:center; gap:10px; text-decoration:none}
    .brand-logo{height:28px; width:auto}
    .brand-text{font-weight:800; color:#fff}

    .menu{
      display:flex; align-items:center; gap:26px;
      margin-left:auto; margin-right:auto;
    }
    .menu a{
      position:relative; text-decoration:none; color:rgba(255,255,255,.9);
      font-weight:600; padding:10px 4px; transition:.15s color, .15s opacity;
    }
    .menu a:hover,.menu a.active{ color:#fff }
    .menu a.active::after,.menu a:hover::after{
      content:""; position:absolute; left:0; right:0; bottom:4px; height:2px;
      background:#fff; border-radius:2px; opacity:.9;
    }

    .actions{display:flex; align-items:center; gap:10px}
    /* HAPUS sign-in; hanya tombol Login */
    .cta{
      background:#fff; color:var(--orange); text-decoration:none; font-weight:800;
      padding:9px 14px; border-radius:999px; box-shadow:0 10px 22px rgba(0,0,0,.15);
    }
    .cta:hover{ color:var(--orange-dark); background:#fff }

    /* Tombol burger */
    .burger{display:none; border:0; background:transparent; width:36px; height:36px; border-radius:8px}
    .burger span{display:block; height:2px; background:#fff; margin:6px 0}

    /* ===== Mobile ===== */
    @media (max-width:992px){
      .menu{
        position:fixed; inset:56px 12px auto 12px; background:#fff; border:1px solid var(--border);
        border-radius:14px; padding:10px; display:none; flex-direction:column; gap:6px;
        z-index:1101; box-shadow:0 20px 40px rgba(15,23,42,.12)
      }
      .menu.show{ display:flex }
      .menu.show a{ color:var(--ink) }
      .menu.show a::after{ display:none }
      .burger{ display:inline-block }
    }
  </style>

  @stack('styles')
</head>
<body>
  {{-- NAVBAR ORANYE --}}
  <header class="nav-orange">
    <div class="nav-inner">
      <a class="brand" href="{{ route('home') }}" aria-label="Beranda">
        <img src="{{ asset('gambar/logo 1 1.png') }}" alt="BPBD" class="brand-logo" onerror="this.style.display='none'">
        <span class="brand-text">BPBD Kabupaten Malang</span>
      </a>

      <nav class="menu" id="mainMenu" aria-label="Menu utama">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"></a>
        <a href="{{ url('/#map') }}"></a>
        <a href="{{ url('/#stat') }}"></a>
        <a href="{{ url('/#ringkasan') }}"></a>
        <a href="{{ url('/#kontak') }}"></a>
      </nav>

      <div class="actions">
        @auth
          <a class="text-white text-decoration-none fw-bold" href="{{ route('landing') }}">
            <i class="bi bi-speedometer2 me-1"></i>Dashboard
          </a>
          <a class="cta" href="{{ route('logout') }}"
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        @else
          {{-- Hanya tombol LOGIN (menggantikan Create Account) --}}
          <a class="cta" href="{{ route('home') }}">Login</a>
        @endauth

        <button class="burger" type="button" data-target="#mainMenu" aria-label="Buka menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <main class="container container-narrow my-3">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle menu mobile
    document.addEventListener('DOMContentLoaded',()=> {
      const burger=document.querySelector('.burger');
      const menu=document.querySelector(burger?.getAttribute('data-target')||'#mainMenu');
      if(burger&&menu){ burger.addEventListener('click',()=> menu.classList.toggle('show')); }
    });
  </script>
  @stack('scripts')
</body>
</html>