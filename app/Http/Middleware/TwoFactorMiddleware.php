<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Check if user is logged in
        if ($user) {
            // 2. Check if verified
            if (!$request->session()->has('two_factor_verified')) {
                if ($request->is('/')) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect('/');
                }

                // Allow access to 2fa routes and logout
                if ($request->is('two-factor-auth*') || $request->is('logout') || $request->is('email/*')) {
                    return $next($request);
                }

                // Redirect to 2FA verification
                return redirect()->route('two-factor.index');
            }
        }

        return $next($request);
    }
}
