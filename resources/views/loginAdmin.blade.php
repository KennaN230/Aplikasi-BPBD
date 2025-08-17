<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { 
      margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; 
    }
    body {
      background-color: #224E97;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      background-color: #0B1C3F;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      width: 350px;
      color: white;
    }
    .login-container h2 { 
      text-align: center; 
      margin-bottom: 20px; 
      font-weight: 600; 
    }
    .alert { 
      padding: 10px; 
      border-radius: 5px; 
      text-align: center; 
      margin-bottom: 15px; 
      font-size: 14px; 
    }
    .alert-success { 
      background-color: #22c55e; 
      color: white; 
    }
    .alert-danger { 
      background-color: #ef4444; 
      color: white; 
    }
    label { 
      font-weight: 600; 
      font-size: 14px; 
      margin-bottom: 5px; 
      display: block; 
    }
    input { 
      width: 100%; 
      padding: 10px 12px; 
      border: none; 
      border-radius: 8px; 
      outline: none; 
      font-size: 12px; 
      background-color: #f8f9fa;
      color: #212529;
    }
    .input-group { 
      position: relative; 
      margin-bottom: 15px; 
    }
    .input-group input { 
      padding-right: 40px; 
    }
    .toggle-password { 
      position: absolute; 
      top: 50%; 
      right: 12px; 
      transform: translateY(-50%); 
      cursor: pointer; 
      width: 20px; 
      height: 20px; 
    }
    .forgot-password { 
      text-align: right; 
      margin-bottom: 15px; 
    }
    .forgot-password a { 
      font-size: 12px; 
      color: #B0C4DE; 
      text-decoration: none; 
    }
    .forgot-password a:hover { 
      text-decoration: underline; 
    }
    .login-btn { 
      background-color: #F46A1F; 
      color: white; 
      border: none; 
      width: 100%; 
      padding: 10px; 
      border-radius: 6px; 
      font-weight: bold; 
      cursor: pointer; 
      font-size: 14px; 
      transition: background 0.3s ease; 
    }
    .login-btn:hover { 
      background-color: #d85a18; 
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login</h2>

    {{-- Pesan sukses dari registrasi --}}
    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    {{-- Pesan error login --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0" style="list-style: none; padding: 0; margin: 0;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST" autocomplete="off">
      @csrf
      <label for="nama">Nama</label>
      <div class="input-group">
        <input type="text" name="nama" id="nama" placeholder="Masukkan Nama Anda" value="{{ old('nama') }}" required autofocus>
      </div>

      <label for="password">Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Masukkan Password Anda" required autocomplete="current-password">
        <img src="{{ asset('gambar/eyeoff.png') }}" class="toggle-password" id="togglePassword" alt="toggle password visibility">
      </div>

      <div class="forgot-password">
        <a href="{{ route('password.request') }}">Lupa Password?</a>
      </div>

      <button type="submit" class="login-btn">LOGIN</button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.getElementById("password");
      const toggleIcon = document.getElementById("togglePassword");

      if (toggleIcon) {
        toggleIcon.addEventListener("click", () => {
          if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.src = "{{ asset('gambar/eyeon.png') }}";
            toggleIcon.alt = "Hide password";
          } else {
            passwordInput.type = "password";
            toggleIcon.src = "{{ asset('gambar/eyeoff.png') }}";
            toggleIcon.alt = "Show password";
          }
        });
      }

      // Fokus ke field nama jika ada error
      @if($errors->any())
        document.getElementById('nama').focus();
      @endif
    });
  </script>

</body>
</html>
