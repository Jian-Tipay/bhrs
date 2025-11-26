<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('content.user.user_profile');
    }
    
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate inputs
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'contact_number' => 'nullable|string|max:15',
                'guardian_number' => 'nullable|string|max:15',
                'first_name' => 'nullable|string|max:50',
                'last_name' => 'nullable|string|max:50',
                'program' => 'nullable|string|max:100',
                'year_level' => 'nullable|in:1st Year,2nd Year,3rd Year,4th Year,Graduate',
                'gender' => 'nullable|in:Male,Female,Other',
                'password' => 'nullable|string|min:8',
                'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
            ]);

            // Handle profile picture
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                
                // Create directory if it doesn't exist
                $profilesPath = public_path('assets/profiles');
                if (!File::exists($profilesPath)) {
                    File::makeDirectory($profilesPath, 0755, true);
                }

                // Delete old picture if exists
                if ($user->profile_picture && File::exists(public_path($user->profile_picture))) {
                    File::delete(public_path($user->profile_picture));
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Move file to public/assets/profiles
                $file->move($profilesPath, $filename);

                // Update profile picture path
                $user->profile_picture = 'assets/profiles/' . $filename;
            }

            // Update all fields using Eloquent
            $user->name = $request->name;
            $user->email = $request->email;
            $user->contact_number = $request->contact_number;
            $user->guardian_number = $request->guardian_number;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->program = $request->program;
            $user->year_level = $request->year_level;
            $user->gender = $request->gender;

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Save all changes at once
            $user->save();

            return redirect()->route('profile')->with('success', 'Profile updated successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('profile')
                ->withErrors($e->validator)
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Profile Update Error', [
                'message' => $e->getMessage(),
            ]);
            
            return redirect()->route('profile')->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
}