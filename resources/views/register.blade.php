<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <style>
        body {
            margin: 0;
            background-color: #243F86;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        .register-box {
            background-color: #0B1C3F;
            padding: 30px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        h2 {
            color: white;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        label {
            display: block;
            color: white;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .input-group {
            position: relative;
            width: 100%;
        }
        input, select {
            width: 100%;
            padding: 10px 40px 10px 10px;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 14px;
            margin-bottom: 5px;
            box-sizing: border-box;
        }
        input::placeholder {
            color: #6b7280;
        }
        .eye-icon {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 20px;
            height: 20px;
        }
        .register-box button {
            display: block;
            margin: 20px auto 0 auto;
            width: 100%;
            background-color: #f97316; 
            border: none;
            padding: 10px;
            color: white;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        .register-box button:hover {
            background-color: #ea580c;
        }
        .error-message {
            color: #f87171;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .success-message {
            color: #4ade80;
            margin-bottom: 10px;
            text-align: center;
        }
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

        <form method="POST" action="{{ route('register.process') }}" enctype="multipart/form-data">
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
            <button type="submit">REGISTER</button>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, icon) {
            const field = document.getElementById(fieldId);
            if (field.type === "password") {
                field.type = "text";
                icon.src = "{{ asset('gambar/eyeon.png') }}";
            } else {
                field.type = "password";
                icon.src = "{{ asset('gambar/eyeoff.png') }}";
            }
        }
    </script>
</body>
</html>