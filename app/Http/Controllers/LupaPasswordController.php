<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class LupaPasswordController extends Controller
{
    public function create()
    {
        return view('users.lupa-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function edit(Request $request, $token)
    {
        return view('users.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required','email'],
            'password'              => ['required','confirmed','min:6'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // JANGAN chaining setRememberToken -> save()
                $user->forceFill([
                    'password' => Hash::make($password),
                ]);

                $user->setRememberToken(str()->random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.')
            : back()->withErrors(['email' => __($status)])
                  ->withInput($request->only('email'));
    }
}