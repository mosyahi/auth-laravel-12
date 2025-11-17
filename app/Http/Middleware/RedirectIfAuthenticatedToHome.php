<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedToHome
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !$request->session()->has('otp_user_id')) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
