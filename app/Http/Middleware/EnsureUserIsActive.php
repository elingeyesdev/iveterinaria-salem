<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (int) $user->estado !== 1) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Su cuenta esta desactivada.'], 403);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Su cuenta esta desactivada. Contacte al administrador.',
            ]);
        }

        return $next($request);
    }
}
