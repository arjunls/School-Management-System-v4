<?php

namespace App\Modules\Security\Middleware;

use Closure;
use Illuminate\Http\Request;

class Check2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user && $user->twoFactorAuth && $user->twoFactorAuth->is_enabled) {
            if (!$request->session()->has('2fa_verified') || !$request->session()->get('2fa_verified')) {
                return redirect()->route('security.2fa.setup');
            }
        }

        return $next($request);
    }
}