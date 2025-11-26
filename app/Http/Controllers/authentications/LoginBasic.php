<?php

namespace App\Http\Controllers\Authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

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
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ]);

        // Verify reCAPTCHA
        if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()->withErrors([
                'studId' => 'reCAPTCHA verification failed. Please try again.'
            ])->onlyInput('studId');
        }

        // Check if too many login attempts
        $this->checkTooManyFailedAttempts($request);

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

            // Clear the rate limiter on successful login
            RateLimiter::clear($this->throttleKey($request));

            // Log the user in
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role === 'user') {
                return redirect()->route('dashboard.user');
            } elseif ($user->role === 'landlord') {
                return redirect()->route('landlord.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // If role doesn't match any expected values
            Auth::logout();
            abort(403, 'Unauthorized role.');
        }

        // Increment failed attempts - 60 seconds = 1 minute
        RateLimiter::hit($this->throttleKey($request), 60);

        // Authentication failed
        return back()->withErrors([
            'studId' => 'The provided credentials do not match our records.'
        ])->onlyInput('studId');
    }

    /**
     * Verify reCAPTCHA response
     */
    protected function verifyRecaptcha($response, $remoteIp)
    {
        $secretKey = config('services.recaptcha.secret_key');
        
        if (empty($secretKey)) {
            // If reCAPTCHA is not configured, skip verification (for development)
            return true;
        }

        try {
            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $response,
                'remoteip' => $remoteIp,
            ]);

            $result = $verifyResponse->json();

            // For reCAPTCHA v3, check score (0.0 to 1.0, higher is better)
            // Score threshold: 0.5 is recommended (adjust based on your needs)
            return isset($result['success']) && 
                   $result['success'] === true && 
                   isset($result['score']) && 
                   $result['score'] >= 0.5;

        } catch (\Exception $e) {
            // Log the error and allow login to prevent service disruption
            \Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * Check if user has too many failed login attempts
     */
    protected function checkTooManyFailedAttempts(Request $request)
    {
        $maxAttempts = 3;

        if (RateLimiter::tooManyAttempts($this->throttleKey($request), $maxAttempts)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));
            
            throw ValidationException::withMessages([
                'studId' => "Too many login attempts. Please try again in {$seconds} second(s).",
            ]);
        }
    }

    /**
     * Get the rate limiting throttle key for the request
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('studId')) . '|' . $request->ip();
    }
}