<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Rating;
use App\Models\PropertyView;

class UserController extends Controller
{
    public function index()
    {
        return view('content.user.user_profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate inputs
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Update name and email
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Update profile picture if uploaded
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            
            // Delete old picture if exists
            if ($user->profile_picture && File::exists(public_path($user->profile_picture))) {
                File::delete(public_path($user->profile_picture));
            }

            // Save to public folder (e.g., public/assets/images/profiles/)
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/profiles'), $filename);

            $user->profile_picture = 'assets/profiles/' . $filename;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}
