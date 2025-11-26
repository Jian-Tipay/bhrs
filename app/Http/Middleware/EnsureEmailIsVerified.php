<?php
// app/Http/Middleware/EnsureEmailIsVerified.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check for users with 'user' role (tenants)
        if (Auth::check() && Auth::user()->role === 'user') {
            // Check if user implements MustVerifyEmail and hasn't verified
            if (Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('dashboard.user')
                    ->with('error', 'Please verify your email address to access this feature.');
            }
        }

        return $next($request);
    }
}