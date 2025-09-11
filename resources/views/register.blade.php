<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{
      background:#243F86;min-height:100vh;display:flex;justify-content:center;align-items:center
    }
    .register-box{
      background:#0B1C3F;color:#fff;width:360px;padding:28px 32px;border-radius:12px;
      box-shadow:0 4px 12px rgba(0,0,0,.3)
    }
    h2{color:#fff;text-align:center;margin-bottom:18px;font-size:24px;font-weight:700}

    label{display:block;color:#fff;font-weight:700;margin:8px 0 6px;font-size:13px}
    .input-group{position:relative;width:100%}
    input,select{
      width:100%;padding:10px 40px 10px 12px;border-radius:10px;border:none;outline:none;
      font-size:13px;background:#f1f5fb;color:#212529
    }
    input::placeholder{color:#6b7280}
    .eye-icon{
      position:absolute;top:50%;right:12px;transform:translateY(-50%);
      cursor:pointer;width:20px;height:20px
    }

    .btn-primary{
      display:block;width:100%;margin-top:16px;background:#f97316;border:none;
      padding:11px;border-radius:8px;color:#fff;font-weight:700;font-size:14px;cursor:pointer;
      transition:background .2s
    }
    .btn-primary:hover{background:#ea580c}

    /* Tombol Kembali (link bergaya tombol) */
    .btn-back{
      display:block;width:100%;margin-top:10px;padding:11px;border-radius:8px;
      background:transparent;border:2px solid #B0C4DE;color:#B0C4DE;
      text-align:center;text-decoration:none;font-weight:700;font-size:14px;transition:all .2s
    }
    .btn-back:hover{background:#0e2b66;color:#e7eefc;border-color:#e7eefc}

    .error-message{color:#f87171;font-size:12px;margin-bottom:6px}
    .success-message{color:#4ade80;margin-bottom:10px;text-align:center}
  </style>
</head>
<body>
  <div class="register-box">
    <h2>Register</h2>

    {{-- Pesan sukses --}}
    @if(session('success'))
      <div class="success-message">{{ session('success') }}</div>
    @endif

    {{-- Tampilkan semua error --}}
    @if($errors->any())
      @foreach($errors->all() as $error)
        <div class="error-message">{{ $error }}</div>
      @endforeach
    @endif

    <form method="POST" action="{{ route('register.process') }}" enctype="multipart/form-data" autocomplete="off">
      @csrf

      <label for="role">Role</label>
      <select id="role" name="role" required>
        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
      </select>

      <label for="nama">Nama</label>
      <input type="text" id="nama" name="nama" placeholder="Masukkan Nama Lengkap" value="{{ old('nama') }}" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Masukkan Email" value="{{ old('email') }}" required>

      <label for="no_hp">Nomor HP</label>
      <input type="tel" id="no_hp" name="no_hp" placeholder="Masukkan Nomor HP" value="{{ old('no_hp') }}" required>

      <label for="password">Password</label>
      <div class="input-group">
        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
        <img src="{{ asset('gambar/eyeoff.png') }}" alt="Show" class="eye-icon" onclick="togglePassword('password', this)">
      </div>

      <label for="password_confirmation">Konfirmasi Password</label>
      <div class="input-group">
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Masukkan Ulang Password" required>
        <img src="{{ asset('gambar/eyeoff.png') }}" alt="Show" class="eye-icon" onclick="togglePassword('password_confirmation', this)">
      </div>

      <button type="submit" class="btn-primary">REGISTER</button>
      <!-- KEMBALI langsung ke halaman awal Admin (dashboard) -->
      <a href="{{ route('admin.home') }}" class="btn-back" role="button">KEMBALI</a>
    </form>
  </div>

  <script>
    function togglePassword(fieldId, icon){
      const field=document.getElementById(fieldId);
      if(field.type==='password'){
        field.type='text'; icon.src="{{ asset('gambar/eyeon.png') }}";
      }else{
        field.type='password'; icon.src="{{ asset('gambar/eyeoff.png') }}";
      }
    }
  </script>
</body>
</html>
