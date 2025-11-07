<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{background:#224E97;display:flex;justify-content:center;align-items:center;min-height:100vh}
    .login-container{background:#0B1C3F;color:#fff;width:360px;padding:28px 32px;border-radius:12px;box-shadow:0 5px 15px rgba(0,0,0,.3)}
    .login-container h2{margin-bottom:18px;font-weight:600;text-align:center}

    .alert{padding:10px 12px;border-radius:8px;font-size:14px;margin-bottom:12px}
    .alert-success{background:#22c55e}
    .alert-danger{background:#ef4444}
    .alert-warning{background:#f59e0b}

    label{font-size:13px;font-weight:600;margin-bottom:6px;display:block}
    .input-group{position:relative;margin-bottom:14px}
    input[type="text"],input[type="password"]{
      width:100%;background:#f1f5fb;color:#212529;border:none;border-radius:10px;
      padding:10px 40px 10px 12px;font-size:13px;outline:none
    }
    .forgot-password{text-align:right;margin-bottom:14px}
    .forgot-password a{font-size:12px;color:#B0C4DE;text-decoration:none}
    .forgot-password a:hover{text-decoration:underline}

    .login-btn{
      background:#F46A1F;color:#fff;border:none;width:100%;padding:11px;border-radius:8px;
      font-weight:700;font-size:14px;cursor:pointer;transition:background .2s ease
    }
    .login-btn:hover{background:#d85a18}

    /* Tombol Kembali (link bergaya tombol) */
    .back-btn{
      margin-top:10px;width:100%;padding:11px;border-radius:8px;
      background:transparent;border:2px solid #B0C4DE;color:#B0C4DE;
      font-weight:700;font-size:14px;text-align:center;text-decoration:none;
      display:inline-block;transition:all .2s ease
    }
    .back-btn:hover{background:#0e2b66;color:#e7eefc;border-color:#e7eefc}

    .toggle-password{
      position:absolute;top:50%;right:10px;transform:translateY(-50%);
      border:0;background:transparent;cursor:pointer;padding:6px;line-height:0
    }
    .toggle-password img{width:20px;height:20px;display:block}
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login</h2>

    {{-- Peringatan (mis. user mencoba akses admin) --}}
    @if(session('warn'))
      <div class="alert alert-warning">{{ session('warn') }}</div>
    @endif

    {{-- Pesan sukses --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pesan status (reset password dsb.) --}}
    @if(session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Error validasi/login --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul style="list-style:none;margin:0;padding:0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST" autocomplete="off">
      @csrf

      <label for="nama">Nama</label>
      <div class="input-group">
        <input type="text" name="nama" id="nama" placeholder="Masukkan Nama Anda"
               value="{{ old('nama') }}" required autocomplete="username" autofocus>
      </div>

      <label for="password">Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Masukkan Password Anda"
               required autocomplete="current-password">
        <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan password">
          <img src="{{ asset('gambar/eyeoff.png') }}" alt="">
        </button>
      </div>

      <div class="forgot-password">
        <a href="{{ route('password.request') }}">Lupa Password?</a>
      </div>

      <button type="submit" class="login-btn">LOGIN</button>
      <!-- KEMBALI langsung ke halaman awal Admin (dashboard) -->
      <a href="{{ route('admin.home') }}" class="back-btn" role="button">KEMBALI</a>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Toggle password eye
      const pwd = document.getElementById('password');
      const btn = document.getElementById('togglePassword');
      const img = btn?.querySelector('img');
      btn?.addEventListener('click', () => {
        const showing = pwd.type === 'text';
        pwd.type = showing ? 'password' : 'text';
        img.src = showing ? "{{ asset('gambar/eyeoff.png') }}" : "{{ asset('gambar/eyeon.png') }}";
        btn.setAttribute('aria-label', showing ? 'Tampilkan password' : 'Sembunyikan password');
      });

      // Fokus ke nama saat ada error
      @if($errors->any())
        document.getElementById('nama')?.focus();
      @endif
    });
  </script>

</body>
</html>