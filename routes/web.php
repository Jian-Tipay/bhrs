<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Analytics;
use App\Http\Controllers\Authentications\LoginBasic;
use App\Http\Controllers\Authentications\RegisterBasic;
use App\Http\Controllers\Authentications\ForgotPasswordBasic;
use App\Http\Controllers\Pages\AccountSettingsAccount;
use App\Http\Controllers\Pages\AccountSettingsConnections;
use App\Http\Controllers\Pages\AccountSettingsNotifications;
use App\Http\Controllers\Pages\MiscError;
use App\Http\Controllers\Pages\MiscUnderMaintenance;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivityLogController;


// Default route
Route::get('/', [LoginBasic::class, 'index'])->name('auth.login');

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/email/verify', function () {
        if (auth()->user()->role !== 'user') {
            return redirect()->route('dashboard.' . auth()->user()->role);
        }
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.user');
        }
        return view('content.authentications.verify-email');
    })->name('verification.notice');


    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard.user')
            ->with('success', 'Email verified successfully! You now have full access to all features.');
    })->middleware('signed')->name('verification.verify');


    Route::post('/email/verification-notification', function (Request $request) {

        if ($request->user()->role !== 'user') {
            return back()->with('error', 'Only tenants need email verification.');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard.user');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');

    })->middleware('throttle:6,1')->name('verification.send');
});



/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'log.activity'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'admin'])->name('dashboard');

    // User Approval
    Route::get('/users/pending', [AdminController::class, 'pendingUsers'])->name('users.pending');
    Route::post('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::post('/users/bulk-approve', [AdminController::class, 'bulkApproveUsers'])->name('users.bulk-approve');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/landlords', [AdminController::class, 'landlords'])->name('users.landlords');
    Route::get('/users/tenants', [AdminController::class, 'tenants'])->name('users.tenants');
    Route::get('/users/{id}', [AdminController::class, 'viewUser'])->name('users.view');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Property Management
    Route::get('/properties', [AdminController::class, 'properties'])->name('properties.index');
    Route::post('/properties/{id}/approve', [AdminController::class, 'approveProperty'])->name('properties.approve');
    Route::post('/properties/{id}/reject', [AdminController::class, 'rejectProperty'])->name('properties.reject');
    Route::delete('/properties/{id}', [AdminController::class, 'deleteProperty'])->name('properties.delete');

    // Notifications
    Route::get('/notifications', [AdminController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [AdminController::class, 'markAllNotificationsRead'])->name('notifications.read-all');

    // Booking Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');

    // Review Management
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview'])->name('reviews.delete');

    // Amenities
    Route::get('/amenities', [AdminController::class, 'amenities'])->name('amenities.index');
    Route::post('/amenities', [AdminController::class, 'createAmenity'])->name('amenities.store');
    Route::put('/amenities/{id}', [AdminController::class, 'updateAmenity'])->name('amenities.update');
    Route::delete('/amenities/{id}', [AdminController::class, 'deleteAmenity'])->name('amenities.delete');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    Route::get('/activity-logs/statistics', [ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');
    Route::post('/activity-logs/clear', [ActivityLogController::class, 'clearOldLogs'])->name('activity-logs.clear');
    Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');

    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminController::class, 'updatePassword'])->name('profile.password');
});



/*
|--------------------------------------------------------------------------
| LANDLORD ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:landlord', 'log.activity'])->group(function () {


    Route::get('/landlord/dashboard', [DashboardController::class, 'landlord'])->name('landlord.dashboard');

    Route::post('/landlord/setup', [LandlordController::class, 'setup'])->name('landlord.setup');

    // Properties
    Route::prefix('landlord/properties')->name('landlord.properties.')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/', [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}/view', [PropertyController::class, 'landlordShow'])->name('view');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
    });

    // Bookings
    Route::prefix('landlord/bookings')->name('landlord.bookings.')->group(function () {
        Route::get('/', [LandlordController::class, 'bookings'])->name('index');
        Route::get('/{booking}', [LandlordController::class, 'viewBooking'])->name('show');
        Route::patch('/{booking}/approve', [LandlordController::class, 'approveBooking'])->name('approve');
        Route::patch('/{booking}/reject', [LandlordController::class, 'rejectBooking'])->name('reject');
        Route::patch('/{booking}/cancel', [LandlordController::class, 'cancelBooking'])->name('cancel');
        Route::patch('/{booking}/complete', [LandlordController::class, 'completeBooking'])->name('complete');
    });

    // Tenants
    Route::prefix('landlord/tenants')->name('landlord.tenants.')->group(function () {
        Route::get('/', [LandlordController::class, 'tenants'])->name('index');
        Route::get('/{booking}', [LandlordController::class, 'viewTenant'])->name('show');
    });

    // Reviews
    Route::prefix('landlord/reviews')->name('landlord.reviews.')->group(function () {
        Route::get('/', [LandlordController::class, 'reviews'])->name('index');
        Route::post('/{review}/reply', [LandlordController::class, 'replyReview'])->name('reply');
    });

    // Reports
    Route::prefix('landlord/reports')->name('landlord.reports.')->group(function () {
        Route::get('/', [LandlordController::class, 'reports'])->name('index');
        Route::get('/export-pdf', [LandlordController::class, 'exportPDF'])->name('export-pdf');
        Route::get('/export-excel', [LandlordController::class, 'exportExcel'])->name('export-excel');
    });

    // Profile
    Route::prefix('landlord/profile')->name('landlord.profile.')->group(function () {
        Route::get('/', [LandlordController::class, 'profile'])->name('index');
        Route::put('/update', [LandlordController::class, 'updateProfile'])->name('update');
        Route::put('/update-password', [LandlordController::class, 'updatePassword'])->name('update-password');
        Route::put('/update-business', [LandlordController::class, 'updateBusiness'])->name('update-business');
        Route::put('/update-notifications', [LandlordController::class, 'updateNotifications'])->name('update-notifications');
        Route::delete('/delete', [LandlordController::class, 'deleteAccount'])->name('delete');
    });

});



/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user', 'log.activity'])->group(function () {


    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('dashboard.user');
    Route::get('/user/profile', [UserController::class, 'index'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

});


// Verified users only
Route::middleware(['auth', 'role:user', 'verified', 'log.activity'])->group(function () {


    // Recommendations
    Route::get('/find-boarding-houses', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/find-boarding-houses/refresh', [RecommendationController::class, 'refresh'])->name('recommendations.refresh');
    Route::post('/find-boarding-houses/track-click/{property}', [RecommendationController::class, 'trackClick'])->name('recommendations.track-click');

    // Preferences
    Route::post('/preferences', [PreferenceController::class, 'store'])->name('preferences.store');
    Route::get('/preferences/edit', [PreferenceController::class, 'edit'])->name('preferences.edit');
    Route::put('/preferences', [PreferenceController::class, 'update'])->name('preferences.update');
    Route::delete('/preferences', [PreferenceController::class, 'destroy'])->name('preferences.destroy');
    Route::get('/preferences/check', [PreferenceController::class, 'checkPreferences'])->name('preferences.check');
    Route::get('/my-preferences', [PreferenceController::class, 'index'])->name('preferences.index');

    // Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/filter/{status}', [BookingController::class, 'filter'])->name('filter');
        Route::get('/create/{property}', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        Route::put('/{booking}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    });

    // Ratings
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::put('/ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::get('/my-ratings', [RatingController::class, 'getUserRatings'])->name('ratings.index');
});



/*
|--------------------------------------------------------------------------
| SHARED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'log.activity'])->group(function () {


    Route::get('/find-boarding-houses', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/find-boarding-houses/refresh', [RecommendationController::class, 'refresh'])->name('recommendations.refresh');
    Route::post('/find-boarding-houses/track-click/{property}', [RecommendationController::class, 'trackClick'])->name('recommendations.track-click');

    // Public property view
    Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('properties.view');

    // Ratings
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::put('/ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::get('/my-ratings', [RatingController::class, 'getUserRatings'])->name('ratings.index');
});



/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::get('/recommendations', [RecommendationController::class, 'getRecommendationsApi']);
    Route::get('/properties/{property}/predict-rating', [RecommendationController::class, 'predictRating']);
});



/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/logout', [Analytics::class, 'logout'])->name('logout');

Route::prefix('auth')->group(function () {
    Route::post('/login-basic', [LoginBasic::class, 'login'])->name('auth.login.process');
    Route::get('/register-basic', [RegisterBasic::class, 'index'])->name('auth.register');
    Route::post('/register', [RegisterBasic::class, 'store'])->name('register.store');
    Route::get('/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth.password.forgot');
});

Route::get('/register', [RegisterBasic::class, 'index'])->name('register');

Route::get('/pending-approval', [RegisterBasic::class, 'pendingApproval'])
    ->name('auth.pending-approval');

Route::prefix('account')->group(function () {
    Route::get('/settings', [AccountSettingsAccount::class, 'index'])->name('account.settings');
    Route::get('/notifications', [AccountSettingsNotifications::class, 'index'])->name('account.notifications');
    Route::get('/connections', [AccountSettingsConnections::class, 'index'])->name('account.connections');
});



// Google OAuth
Route::get('/admin/google/authorize', function () {
    $service = new \App\Services\GoogleMailService();
    return redirect($service->getAuthorizationUrl());
});

Route::get('/admin/google/callback', function (Request $request) {
    $service = new \App\Services\GoogleMailService();
    try {
        $service->handleCallback($request->get('code'));
        return redirect('/admin/dashboard')->with('success', 'Gmail API authorized successfully!');
    } catch (\Exception $e) {
        return redirect('/admin/dashboard')->with('error', 'Failed to authorize Gmail: ' . $e->getMessage());
    }
});
