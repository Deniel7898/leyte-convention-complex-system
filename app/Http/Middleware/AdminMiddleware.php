<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Not logged in
        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ Admin → always allowed
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ✅ Staff → must be verified
        if ($user->role === 'staff') {
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('error', 'Please verify your email first.');
            }

            return $next($request);
        }

        // ❌ Any other role
        abort(403, 'Unauthorized');
    }
}
