<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BPBD Kabupaten Malang</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{
      margin:0; min-height:100vh; display:flex; justify-content:center; align-items:center;
      background:#243F86; padding:20px;
    }

    /* KOTAK UTAMA */
    .container{
      position:relative;           /* supaya tombol back absolute nempel ke kotak */
      width:100%; max-width:420px;
      background:#0B1C3F; color:#fff;
      border-radius:20px; box-shadow:0 8px 24px rgba(0,0,0,.35);
      padding:48px 28px 32px;      /* ruang atas sedikit lebih besar untuk tombol back */
      text-align:center;
    }

    /* Tombol back DI DALAM kotak */
    .back-in{
      position:absolute; top:12px; left:12px;
      display:inline-flex; align-items:center; justify-content:center;
      width:40px; height:40px; border-radius:999px;
      border:1px solid rgba(255,255,255,.35);
      background:rgba(255,255,255,.10);
      color:#E7EEF9; cursor:pointer; transition:.18s;
    }
    .back-in:hover{background:rgba(255,255,255,.18); border-color:rgba(255,255,255,.55)}
    .back-in:focus{outline:2px solid #FF6600; outline-offset:2px}

    .logo-row{
      display:flex; justify-content:center; align-items:center; gap:22px;
      margin-bottom:16px;
    }
    .logo-row img{height:78px}

    h1{
      margin:6px 0 26px; font-size:18px; line-height:1.35; font-weight:600;
    }

    .btn{
      display:block; width:100%; padding:12px 14px; margin:0 auto 14px;
      background:#FF6600; color:#fff; border:none; border-radius:12px;
      font-weight:700; font-size:16px; text-decoration:none; cursor:pointer;
      transition:background .2s ease;
    }
    .btn:hover{background:#e55a00}

    @media (max-width:420px){
      .container{padding:44px 18px 28px}
      .logo-row img{height:68px}
      h1{font-size:17px}
    }
  </style>
</head>
<body>

  <div class="container">
    <!-- Panah kembali DI DALAM kotak -->
    <button class="back-in" type="button" aria-label="Kembali" onclick="goBack()">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M15 18l-6-6 6-6"/>
      </svg>
    </button>

    <div class="logo-row">
      <img src="{{ asset('gambar/Logo 1 1.png') }}" alt="BPBD Logo">
      <img src="{{ asset('gambar/Logo_Kabupaten_Malang 1.png') }}" alt="Kabupaten Malang Logo">
    </div>

    <h1>Selamat Datang Di Pusat Informasi<br>Kejadian Kabupaten Malang</h1>

    <a href="{{ route('login') }}" class="btn">LOGIN</a>
    <a href="{{ route('register') }}" class="btn">REGISTER</a>
  </div>

  <script>
    function goBack(){
      window.location.href = "{{ route('home') }}";
    }
  </script>
</body>
</html>
