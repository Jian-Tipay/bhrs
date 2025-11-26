<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail; 

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
   protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'studID',
        'student_number',
        'contact_number',
        'address', // Added this field
        'program',
        'year_level',
        'gender',
        'guardian_number',
        'profile_picture',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Override to send verification email only for tenants
     */
       public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }


    /**
     * Check if user should verify email (only tenants)
     */
    public function shouldVerifyEmail()
    {
        return $this->role === 'user' && !$this->hasVerifiedEmail();
    }

    // Relationships
    public function landlord()
    {
        return $this->hasOne(Landlord::class);
    }

    public function properties()
    {
        return $this->hasManyThrough(Property::class, Landlord::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper Methods
    public function unreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
    }
    public function views()
    {
        return $this->hasMany(PropertyView::class);
    }

    // Helper Methods

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isTenant()
    {
        return $this->role === 'user';
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isPending()
    {
        return $this->approval_status === 'pending';
    }
}