<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Rating;
use App\Models\Landlord;
use App\Models\Amenity;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;

class AdminController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Admin Dashboard
     */
    public function admin()
    {
        $user = Auth::user();

        // User Statistics
        $totalUsers = User::count();
        $pendingUsers = User::where('approval_status', 'pending')->count();
        $approvedUsers = User::where('approval_status', 'approved')->count();
        $totalLandlords = User::where('role', 'landlord')->count();
        $totalTenants = User::where('role', 'user')->count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)->count();

        // Property Statistics
        $totalProperties = Property::count();
        $activeProperties = Property::where('is_active', 1)->count();
        $pendingProperties = Property::where('is_active', 0)->count();
        $propertiesThisMonth = Property::whereMonth('created_at', Carbon::now()->month)->count();

        // Booking Statistics
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'Pending')->count();
        $activeBookings = Booking::where('status', 'Active')->count();
        $completedBookings = Booking::where('status', 'Completed')->count();

        // Revenue Statistics
        $monthlyRevenue = Booking::where('status', 'Active')
            ->whereMonth('move_in_date', Carbon::now()->month)
            ->join('properties', 'bookings.property_id', '=', 'properties.id')
            ->sum('properties.price');

        // Rating Statistics
        $totalReviews = Rating::count();
        $averageRating = Rating::avg('rating') ?? 0;

        // Recent Activities
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentProperties = Property::with(['landlord.user', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $recentBookings = Booking::with(['user', 'property'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Chart Data - User Growth (last 6 months)
        $userGrowthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $count = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $userGrowthData[$month] = $count;
        }

        // Chart Data - Booking Status Distribution
        $bookingStatusData = [
            'Pending' => Booking::where('status', 'Pending')->count(),
            'Approved' => Booking::where('status', 'Approved')->count(),
            'Active' => Booking::where('status', 'Active')->count(),
            'Completed' => Booking::where('status', 'Completed')->count(),
            'Cancelled' => Booking::where('status', 'Cancelled')->count(),
        ];

        // Chart Data - Revenue (last 6 months)
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $revenue = Booking::where('status', 'Active')
                ->whereMonth('move_in_date', $date->month)
                ->whereYear('move_in_date', $date->year)
                ->join('properties', 'bookings.property_id', '=', 'properties.id')
                ->sum('properties.price');
            $revenueData[$month] = $revenue;
        }

        // Top Properties
        $topProperties = Property::withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderByDesc('ratings_avg_rating')
            ->limit(5)
            ->get();

        return view('content.admin.admin_dashboard', compact(
            'user',
            'totalUsers',
            'pendingUsers',
            'approvedUsers',
            'totalLandlords',
            'totalTenants',
            'newUsersThisMonth',
            'totalProperties',
            'activeProperties',
            'pendingProperties',
            'propertiesThisMonth',
            'totalBookings',
            'pendingBookings',
            'activeBookings',
            'completedBookings',
            'monthlyRevenue',
            'totalReviews',
            'averageRating',
            'recentUsers',
            'recentProperties',
            'recentBookings',
            'userGrowthData',
            'bookingStatusData',
            'revenueData',
            'topProperties'
        ));
    }

    /**
     * Get pending users for approval
     */
    public function pendingUsers(Request $request)
    {
        $query = User::where('approval_status', 'pending')
                     ->where('role', '!=', 'admin');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        $pendingUsers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('content.admin.users.pending', compact('pendingUsers'));
    }

    /**
     * Approve user account
     */
    public function approveUser($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->approval_status === 'approved') {
                return redirect()->back()->with('info', 'User is already approved.');
            }

            $user->approval_status = 'approved';
            $user->approved_by = Auth::id();
            $user->approved_at = now();
            $user->save();

            // Send notifications
            $this->notificationService->notifyUserApproved($user);

            return redirect()->back()->with('success', 'User approved successfully and notification sent.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve user: ' . $e->getMessage());
        }
    }

    /**
     * Reject user account
     */
    public function rejectUser(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        try {
            $user = User::findOrFail($id);

            $user->approval_status = 'rejected';
            $user->approved_by = Auth::id();
            $user->approved_at = now();
            $user->rejection_reason = $request->rejection_reason;
            $user->save();

            // Send notifications
            $this->notificationService->notifyUserRejected($user, $request->rejection_reason);

            return redirect()->back()->with('success', 'User rejected and notification sent.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject user: ' . $e->getMessage());
        }
    }

    /**
     * Bulk approve users
     */
    public function bulkApproveUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $users = User::whereIn('id', $request->user_ids)
                        ->where('approval_status', 'pending')
                        ->get();

            foreach ($users as $user) {
                $user->approval_status = 'approved';
                $user->approved_by = Auth::id();
                $user->approved_at = now();
                $user->save();

                // Send notifications
                $this->notificationService->notifyUserApproved($user);
            }

            return redirect()->back()->with('success', count($users) . ' users approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Bulk approval failed: ' . $e->getMessage());
        }
    }

    /**
     * User Management - List all users
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'name':
                    $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                    break;
                default: // newest
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15);

        return view('content.admin.users.index', compact('users'));
    }

    /**
     * Show landlords only
     */
    public function landlords(Request $request)
    {
        $request->merge(['role' => 'landlord']);
        return $this->users($request);
    }

    /**
     * Show tenants only
     */
    public function tenants(Request $request)
    {
        $request->merge(['role' => 'user']);
        return $this->users($request);
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,landlord,user',
            'password' => 'required|min:8|confirmed',
            'contact_number' => 'nullable|string|max:15',
            'student_number' => 'nullable|string|max:20|unique:users,student_number',
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'contact_number' => $request->contact_number,
            'student_number' => $request->student_number,
            'approval_status' => 'approved', // Admin-created users are auto-approved
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // If landlord, create landlord record
        if ($request->role === 'landlord') {
            Landlord::create([
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Update user
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('content.admin.users.edit', compact('user'));
    }
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,landlord,user',
            'contact_number' => 'nullable|string|max:15',
            'student_number' => 'nullable|string|max:20|unique:users,student_number,' . $user->id,
            'program' => 'nullable|string|max:100',
            'year_level' => 'nullable|in:1st Year,2nd Year,3rd Year,4th Year,Graduate',
            'gender' => 'nullable|in:Male,Female,Other',
        ]);

        $user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'contact_number' => $request->contact_number,
            'student_number' => $request->student_number,
            'program' => $request->program,
            'year_level' => $request->year_level,
            'gender' => $request->gender,
        ]);

        // If role changed to landlord and no landlord record exists
        if ($request->role === 'landlord' && !$user->landlord) {
            Landlord::create([
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('admin.users.view', $user->id)->with('success', 'User updated successfully.');
    }

    /**
     * View specific user
     */
    public function viewUser($id)
    {
        $user = User::with(['ratings', 'bookings', 'approvedBy'])->findOrFail($id);
        
        // Get user statistics
        $bookingsCount = $user->bookings()->count();
        $ratingsCount = $user->ratings()->count();
        
        if ($user->role === 'landlord') {
            $landlord = $user->landlord;
            $properties = $landlord ? $landlord->properties()->withCount('bookings')->get() : collect();
        } else {
            $properties = collect();
        }

        return view('content.admin.users.view', compact('user', 'bookingsCount', 'ratingsCount', 'properties'));
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Property Management - List all properties
     */
    public function properties(Request $request)
    {
        $query = Property::with(['landlord.user', 'images']);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_active', 0);
            } elseif ($request->status === 'approved') {
                $query->where('is_active', 1);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $properties = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('content.admin.properties.index', compact('properties'));
    }

    /**
     * Approve property
     */
    public function approveProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->is_active = 1;
        $property->save();

        return redirect()->back()->with('success', 'Property approved successfully.');
    }

    /**
     * Reject property
     */
    public function rejectProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->is_active = 0;
        $property->save();

        return redirect()->back()->with('success', 'Property rejected.');
    }

    /**
     * Delete property
     */
    public function deleteProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Property deleted successfully.');
    }

    /**
     * Booking Management
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'property']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('content.admin.bookings.index', compact('bookings'));
    }

    /**
     * Review Management
     */
    public function reviews(Request $request)
    {
        $query = Rating::with(['user', 'property']);

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('content.admin.reviews.index', compact('reviews'));
    }

    /**
     * Delete review
     */
    public function deleteReview($id)
    {
        $review = Rating::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Amenities Management
     */
    public function amenities()
    {
        $amenities = Amenity::orderBy('amenity_name')->paginate(15);
        return view('content.admin.amenities.index', compact('amenities'));
    }

    /**
     * Create amenity
     */
    public function createAmenity(Request $request)
    {
        $request->validate([
            'amenity_name' => 'required|string|max:255|unique:amenities,amenity_name'
        ]);

        Amenity::create([
            'amenity_name' => $request->amenity_name
        ]);

        return redirect()->back()->with('success', 'Amenity created successfully.');
    }

    /**
     * Update amenity
     */
    public function updateAmenity(Request $request, $id)
    {
        $request->validate([
            'amenity_name' => 'required|string|max:255|unique:amenities,amenity_name,' . $id . ',amenity_id'
        ]);

        $amenity = Amenity::findOrFail($id);
        $amenity->amenity_name = $request->amenity_name;
        $amenity->save();

        return redirect()->back()->with('success', 'Amenity updated successfully.');
    }

    /**
     * Delete amenity
     */
    public function deleteAmenity($id)
    {
        $amenity = Amenity::findOrFail($id);
        $amenity->delete();

        return redirect()->back()->with('success', 'Amenity deleted successfully.');
    }

    /**
     * Get admin notifications
     */
    public function getNotifications()
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id());
        $unreadCount = Auth::user()->unreadNotificationsCount();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $this->notificationService->markAsRead($id, Auth::id());
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());
        return response()->json(['success' => true]);
    }

    /**
     * Activity Logs
     */
    public function activityLogs()
    {
        return view('content.admin.activity-logs');
    }

    /**
     * Reports
     */
    public function reports()
    {
        return view('content.admin.reports.index');
    }

    /**
     * Settings
     */
    public function settings()
    {
        return view('content.admin.settings.index');
    }

    /**
     * Profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('content.admin.profile', compact('user'));
    }

    /**
     * Update Profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}