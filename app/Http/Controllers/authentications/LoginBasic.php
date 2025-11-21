<?php

namespace App\Http\Controllers\Authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginBasic extends Controller
{
    // Show login form
    public function index()
    {
        return view('content.authentications.auth-login-basic');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'studId'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Try to find user by studID, student_number, or email
        $user = User::where('studID', $credentials['studId'])
                    ->orWhere('student_number', $credentials['studId'])
                    ->orWhere('email', $credentials['studId'])
                    ->first();

        // Check if user exists and password matches
        if ($user && Hash::check($credentials['password'], $user->password)) {
            
            // Check approval status for landlords only
            if ($user->role === 'landlord' && $user->approval_status !== 'approved') {
                $statusMessage = match($user->approval_status) {
                    'pending' => 'Your landlord account is pending admin approval. Please wait for approval before logging in.',
                    'rejected' => 'Your landlord account registration was rejected. Please contact support for more information.',
                    default => 'Your account is not yet approved.'
                };
                
                return back()->withErrors([
                    'studId' => $statusMessage
                ])->onlyInput('studId');
            }

            // Log the user in
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role === 'user') {
                return redirect()->route('dashboard.user');
            } elseif ($user->role === 'landlord') {
                return redirect()->route('dashboard.landlord');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // If role doesn't match any expected values
            Auth::logout();
            abort(403, 'Unauthorized role.');
        }

        // Authentication failed
        return back()->withErrors([
            'studId' => 'The provided credentials do not match our records.'
        ])->onlyInput('studId');
    }
}