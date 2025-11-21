<?php

namespace App\Http\Controllers;

use App\Models\StudentPreference;
use App\Models\PreferredAmenity;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PreferenceController extends Controller
{
    /**
     * Store new preferences (initial onboarding)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_min' => 'required|numeric|min:1000|max:10000',
            'budget_max' => 'required|numeric|min:1000|max:10000|gt:budget_min',
            'preferred_distance' => 'required|numeric|min:0.1|max:5',
            'room_type' => 'required|in:Single,Shared,Any',
            'gender_preference' => 'required|in:Male Only,Female Only,Co-ed,Any',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer|exists:amenities,amenity_id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $preference = StudentPreference::updateOrCreate(
                ['user_id' => $userId],
                [
                    'budget_min' => $request->budget_min,
                    'budget_max' => $request->budget_max,
                    'preferred_distance' => $request->preferred_distance,
                    'room_type' => $request->room_type,
                    'gender_preference' => $request->gender_preference,
                ]
            );

            // Refresh amenities
            PreferredAmenity::where('preference_id', $preference->preference_id)->delete();

            if ($request->filled('amenities')) {
                foreach ($request->amenities as $id) {
                    PreferredAmenity::create([
                        'preference_id' => $preference->preference_id,
                        'amenity_id' => $id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('dashboard.user')
                ->with('success', 'Your preferences have been saved!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Preference Save Error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to save preferences. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show My Preferences Page (View Only)
     */
    public function index()
    {
        $userId = auth()->id();

        $preferences = StudentPreference::where('user_id', $userId)->first();
        $amenities = collect();

        if ($preferences) {
            $amenities = DB::table('preferred_amenities')
                ->join('amenities', 'preferred_amenities.amenity_id', '=', 'amenities.amenity_id')
                ->where('preferred_amenities.preference_id', $preferences->preference_id)
                ->select('amenities.amenity_name')
                ->get();
        }

        return view('content.preferences.index', compact('preferences', 'amenities'));
    }

    /**
     * Show Edit Preferences Form (AJAX modal)
     */
    public function edit()
    {
        $userId = Auth::id();
        $preference = StudentPreference::with('preferredAmenities')->where('user_id', $userId)->first();
        $amenities = Amenity::all();

        if (!$preference) {
            return response()->json(['success' => false, 'message' => 'Preferences not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'preference' => $preference,
            'amenities' => $amenities
        ]);
    }

    /**
     * Update preferences via AJAX
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_min' => 'required|numeric|min:1000|max:10000',
            'budget_max' => 'required|numeric|min:1000|max:10000|gt:budget_min',
            'preferred_distance' => 'required|numeric|min:0.1|max:5',
            'room_type' => 'required|in:Single,Shared,Any',
            'gender_preference' => 'required|in:Male Only,Female Only,Co-ed,Any',
            'amenities' => 'nullable|array',
            'amenities.*' => 'integer|exists:amenities,amenity_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $userId = auth()->id();
            $preference = StudentPreference::where('user_id', $userId)->firstOrFail();

            $preference->update([
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'preferred_distance' => $request->preferred_distance,
                'room_type' => $request->room_type,
                'gender_preference' => $request->gender_preference,
            ]);

            PreferredAmenity::where('preference_id', $preference->preference_id)->delete();

            if ($request->filled('amenities')) {
                foreach ($request->amenities as $id) {
                    PreferredAmenity::create([
                        'preference_id' => $preference->preference_id,
                        'amenity_id' => $id
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Preference Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating preferences.'
            ], 500);
        }
    }

    /**
     * Delete preferences
     */
    public function destroy()
    {
        $userId = Auth::id();

        try {
            $preference = StudentPreference::where('user_id', $userId)->firstOrFail();

            PreferredAmenity::where('preference_id', $preference->preference_id)->delete();
            $preference->delete();

            \Cache::forget("recommendations_user_{$userId}");

            return redirect()->route('dashboard.user')
                ->with('success', 'Your preferences have been removed.');
        } catch (\Exception $e) {
            Log::error('Preference Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete preferences.');
        }
    }

    /**
     * API: Check if user already has preferences
     */
    public function checkPreferences()
    {
        $hasPreferences = StudentPreference::where('user_id', Auth::id())->exists();
        return response()->json(['has_preferences' => $hasPreferences]);
    }
}
