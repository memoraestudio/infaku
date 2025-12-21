<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->session()->get('user');

        if (!$user) {
            return redirect('/login')->withErrors(['access' => 'Silakan login terlebih dahulu.']);
        }

        // Check if user role is in allowed roles
        if (!in_array($user['role_id'], $roles)) {
            return redirect('/dashboard')->withErrors(['access' => 'Anda tidak memiliki akses ke halaman ini.']);
        }

        return $next($request);
    }
}
