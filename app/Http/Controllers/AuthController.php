<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Log;

class AuthController extends Controller
{
    // Menampilkan form login admin
    public function showLoginForm()
    {
        return view('loginAdmin');
    }

    // Menentukan kolom yang digunakan untuk login
    public function username()
    {
        return 'nama'; // Kolom yang digunakan untuk login
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        Log::info('Login attempt for user: ' . $request->nama);

        // Cari user berdasarkan nama
        $user = User::where('nama', $request->nama)->first();

        if (!$user) {
            Log::warning('User not found: ' . $request->nama);
            return back()->withErrors([
                'nama' => 'Nama tidak ditemukan'
            ])->withInput();
        }

        // Verifikasi password
        Log::info('Stored password hash: ' . $user->password);
        Log::info('Input password: ' . $request->password);
        Log::info('Hash check result: ' . (Hash::check($request->password, $user->password) ? 'true' : 'false'));

        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Failed login attempt for user: ' . $request->nama);
            return back()->withErrors([
                'password' => 'Password salah'
            ])->withInput();
        }

        // Login user
        Auth::login($user);
        Log::info('User logged in successfully: ' . $request->nama);
        return redirect()->intended('/dashboard');
    }

    // Menampilkan form register
    public function showRegisterForm()
    {
        return view('register');
    }

    // Proses registrasi
    public function registerProcess(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:Userr,email', // Sesuaikan nama tabel (Userr) dengan database
            'no_hp' => 'required|string|max:15',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:User,Admin'
        ]);

        Log::info('New user registration: ' . $request->nama);

        // Buat user baru
        User::create([
            'role' => $request->role, // Gunakan 'role' (huruf kecil) sesuai dengan database
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password), // Enkripsi password
            'photo' => '', // Default kosong
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Menampilkan form lupa password
    public function showForgotPasswordForm()
    {
        return view('LupaPassword'); // Konsisten dengan nama file view
    }

    // Mengirimkan link reset password ke email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:user,email' // Pastikan tabel Userr, bukan user
        ]);

        Log::info('Password reset request for email: ' . $request->email);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}