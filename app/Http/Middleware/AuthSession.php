<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('user')) {
            return redirect('/login')->withErrors(['access' => 'Silakan login terlebih dahulu.']);
        }

        return $next($request);
    }
}
