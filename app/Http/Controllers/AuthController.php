<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /** Halaman login */
    public function showLoginForm()
    {
        return view('loginAdmin');
    }

    /** Proses login (khusus admin dashboard) */
    public function login(Request $request)
    {
        $request->validate([
            'nama'     => ['required','string'],
            'password' => ['required','string','min:6'],
        ]);

        // Boleh pakai email atau nama
        $field = str_contains($request->nama, '@') ? 'email' : 'nama';
        $user  = User::where($field, $request->nama)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['nama' => 'Nama/Email atau password salah'])->withInput();
        }

        // HARUS admin untuk bisa masuk dashboard ini
        $role = strtolower(trim($user->role ?? ''));
        if (!in_array($role, ['admin', 'administrator'], true)) {
            return back()->withErrors(['nama' => 'Akun ini bukan admin.'])->withInput();
        }

        // WAJIB disetujui
        if (!in_array(strtolower($user->status ?? ''), ['approved', 'aktif'], true)) {
            return back()->withErrors(['nama' => 'Akun Anda belum disetujui admin.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /** Halaman register */
    public function showRegisterForm()
    {
        return view('register');
    }

    /** Proses register: selalu pending */
    public function registerProcess(Request $request)
    {
        $request->validate([
            'nama'                  => ['required','string','max:255'],
            'email'                 => ['required','email','unique:userr,email'],
            'no_hp'                 => ['required','string','max:30'],
            'password'              => ['required','confirmed','min:6'],
            'role'                  => ['required','in:User,Admin'],
        ]);

        User::create([
            'role'     => $request->role,
            'nama'     => $request->nama,
            'email'    => $request->email,
            'no_hp'    => $request->no_hp,
            'password' => Hash::make($request->password),
            'photo'    => '',
            'status'   => 'pending',     // ⬅️ penting
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil dikirim. Tunggu persetujuan admin.');
    }

    /** Lupa password (opsional) */
    public function showForgotPasswordForm()
    {
        return view('LupaPassword');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => ['required','email','exists:userr,email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /** Profil */
    public function editProfile()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'nama'     => ['required','string','max:100'],
            'email'    => [ 'required','email', Rule::unique('userr','email')->ignore($user->id_user,'id_user') ],
            'password' => ['nullable','min:6','confirmed'],
        ]);

        if (!empty($data['password'])) $data['password'] = Hash::make($data['password']);
        else unset($data['password']);

        $user->update($data);

        return back()->with('ok','Profil berhasil diperbarui');
    }

    /** Logout */
    public function logout(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->forceFill(['last_seen_at' => now()])->saveQuietly();
        }
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
