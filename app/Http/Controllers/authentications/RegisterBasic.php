<?php

namespace App\Http\Controllers\Authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Landlord;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Registered;

class RegisterBasic extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return view('content.authentications.auth-register-basic');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'studId' => 'required|string|max:255|unique:users,studID',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:user,landlord',
            'contact_number' => 'nullable|string|max:15',
        ];

        if ($request->role === 'user') {
            $rules['program'] = 'required|string|max:100';
            $rules['year_level'] = 'required|string|in:1st Year,2nd Year,3rd Year,4th Year,Graduate';
        } elseif ($request->role === 'landlord') {
            $rules['phone'] = 'required|string|max:20';
            $rules['company_name'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $nameParts = explode(' ', $request->name, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $userData = [
                'name' => $request->name,
                'studID' => $request->studId,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'contact_number' => $request->contact_number,
                'approval_status' => $request->role === 'user' ? 'approved' : 'pending',
            ];

            if ($request->role === 'user') {
                $userData['student_number'] = $request->studId;
                $userData['program'] = $request->program;
                $userData['year_level'] = $request->year_level;
            }

            $user = User::create($userData);

            if ($request->role === 'landlord') {
                Landlord::create([
                    'user_id' => $user->id,
                    'phone' => $request->phone,
                    'company_name' => $request->company_name,
                ]);

                // Notify admins for landlord registration
                $this->notificationService->notifyAdminsNewRegistration(
                    $user->id,
                    $user->name,
                    $user->email
                );
            }

            DB::commit();

            // ✅ Handle tenant (user) registration with email verification
            if ($request->role === 'user') {
                // Auto-login the tenant
                auth()->login($user);
                
                // Trigger the email verification notification
                event(new Registered($user));

                // Redirect to verification notice page
                return redirect()->route('verification.notice');
            }

            // 🕒 Landlords wait for admin approval (no email verification needed)
            return redirect()->route('auth.pending-approval')
                ->with('success', 'Registration successful! Your landlord account is pending admin approval. You will receive an email once approved.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', ['exception' => $e]);

            return back()->withErrors([
                'error' => $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Show pending approval page
     */
    public function pendingApproval()
    {
        return view('content.authentications.auth-pending-approval');
    }
}