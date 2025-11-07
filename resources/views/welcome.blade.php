<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informasi Kejadian Kabupaten Malang</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    :root{
      --orange:#EA620D;
      --fab-bg: rgba(6,18,44,.72);      /* navy transparan */
      --ring:   rgba(255,255,255,.28);  /* cincin luar */
      --inner:  rgba(255,255,255,.22);  /* cincin dalam */
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:'Poppins',sans-serif;
      background:url('{{ asset('gambar/BPBD.jpg') }}') no-repeat center center/cover;
      min-height:100vh;
      display:flex; align-items:center; justify-content:center;
      color:#fff;
    }

    .overlay{
      background: rgba(0,0,0,.40);
      backdrop-filter: blur(5px);
      border-radius:16px;
      padding:40px 28px;
      width:min(1000px, 96vw);
      box-shadow:0 20px 60px rgba(0,0,0,.25);
      text-align:center;
    }

    .logo-container{ display:flex; justify-content:center; gap:20px; margin-bottom:18px }
    .logo-container img{ height:110px; width:auto }

    h1{ font-size:28px; font-weight:700; margin:6px 0 }
    p.subtitle{ font-size:16px; opacity:.95; margin-bottom:26px }

    .button-container{ display:flex; justify-content:center; gap:28px; flex-wrap:wrap; margin-top:22px }
    .btn{
      background-color:var(--orange); color:#fff; text-decoration:none;
      padding:12px 30px; font-weight:700; border-radius:10px;
      box-shadow:0 12px 26px rgba(234,98,13,.35);
      transition:transform .1s ease, filter .2s ease;
    }
    .btn:hover{ transform:translateY(-1px); filter:brightness(0.95) }

    /* === FAB Back (pojok kiri atas) === */
    .fab-back{
      position:fixed; top:22px; left:22px; z-index:1000;
      width:54px; height:54px; border-radius:50%;
      background:var(--fab-bg);
      display:grid; place-items:center;
      color:#fff; text-decoration:none;
      box-shadow:0 10px 28px rgba(0,0,0,.35);
      border:1.5px solid var(--ring);
      backdrop-filter: blur(6px);
      transition: transform .15s ease;
    }
    .fab-back::after{
      /* cincin tipis di dalam */
      content:""; position:absolute; inset:7px; border-radius:50%;
      border:1.5px solid var(--inner);
    }
    .fab-back:hover{ transform:translateX(-2px) }
    .fab-back svg{ display:block }

    @media (max-width:540px){
      .logo-container img{ height:84px }
      h1{ font-size:22px }
      p.subtitle{ font-size:14px }
      .fab-back{ top:14px; left:14px; width:50px; height:50px }
      .fab-back::after{ inset:6px }
    }
  </style>
</head>
<body>
  <!-- FAB kembali ke Landing -->
  <a href="{{ route('landing') }}" class="fab-back" aria-label="Kembali ke Landing Page">
    <!-- Ikon panah kiri (SVG) -->
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M15 18l-6-6 6-6"/>
    </svg>
  </a>

  <div class="overlay">
    <div class="logo-container">
      <img src="{{ asset('gambar/Logo_Kabupaten_Malang 1.png') }}" alt="Logo BPBD Kabupaten Malang">
      <img src="{{ asset('gambar/logoBPBD.png') }}" alt="Logo Pemerintah Kabupaten Malang">
    </div>

    <h1>INFORMASI KEJADIAN KABUPATEN MALANG</h1>
    <p class="subtitle">
      Pusat Pengendalian Operasi Penanggulangan Bencana<br/>
      Badan Penanggulangan Bencana Daerah Kabupaten Malang
    </p>

    <div class="button-container">
      <a href="/admin" class="btn">ADMIN</a>
      <a href="/user" class="btn">USER</a>
    </div>
  </div>
</body>
</html>