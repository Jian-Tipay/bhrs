<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerifiedForTenants
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If no user is authenticated, let the auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Only check verification for tenants (role = 'user')
        if ($user->role === 'user') {
            // If tenant hasn't verified email, redirect to verification notice
            if (!$user->hasVerifiedEmail()) {
                return $request->expectsJson()
                    ? abort(403, 'Your email address is not verified.')
                    : Redirect::route('verification.notice');
            }
        }

        // For landlords and admins, or verified tenants, continue
        return $next($request);
    }
}