<?php
// app/Http/Middleware/CheckApprovalStatus.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovalStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Allow admins to bypass approval check
            if ($user->role === 'admin') {
                return $next($request);
            }

            // Check if user is pending approval
            if ($user->approval_status === 'pending') {
                Auth::logout();
                return redirect('/login')->with('warning', 
                    'Your account is pending approval. Please wait for admin confirmation.');
            }

            // Check if user is rejected
            if ($user->approval_status === 'rejected') {
                Auth::logout();
                return redirect('/login')->with('error', 
                    'Your account registration was not approved. Reason: ' . $user->rejection_reason);
            }
        }

        return $next($request);
    }
}
