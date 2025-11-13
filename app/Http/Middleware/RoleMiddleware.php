<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) return redirect()->route('login');

        $userRole = strtolower(trim($user->role ?? ''));
        $roles    = array_map(fn($r)=>strtolower(trim($r)), $roles);

        if (!empty($roles) && !in_array($userRole, $roles, true)) {
            return $request->expectsJson()
                ? response()->json(['message'=>'Anda tidak memiliki akses ke halaman ini.'], 403)
                : abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // ——— catat last_seen_at, skip asset/static ———
        $path = $request->path();
        if (!preg_match('#^(?:storage|images|img|css|js|vendor|debugbar)#', $path)) {
            try {
                $now  = now();
                $last = $user->last_seen_at; // Carbon|null karena sudah di-cast
                if (!$last || $last->lt($now->copy()->subSeconds(1))) {
                    $user->forceFill(['last_seen_at' => $now])->saveQuietly();
                }
            } catch (\Throwable $e) {
                \Log::warning('Gagal update last_seen_at', ['err'=>$e->getMessage()]);
            }
        }

        return $next($request);
    }
}