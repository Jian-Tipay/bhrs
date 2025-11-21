<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'studID',
        'email',
        'password',
        'role',
        'profile_picture',
        // Additional student fields
        'student_number',
        'first_name',
        'last_name',
        'program',
        'year_level',
        'gender',
        'contact_number',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }
    public function unreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }
        public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if user is pending approval
     */
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if user is rejected
     */
    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }



    // Relationships
    public function landlord()
    {
        return $this->hasOne(Landlord::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }

    public function preferences()
    {
        return $this->hasOne(StudentPreference::class, 'user_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(PropertyView::class, 'user_id', 'id');
    }

    public function recommendationLogs()
    {
        return $this->hasMany(RecommendationLog::class, 'user_id', 'id');
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'user');
    }

    public function scopeLandlords($query)
    {
        return $query->where('role', 'landlord');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->name;
    }

    // Check if user is a specific role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isLandlord()
    {
        return $this->role === 'landlord';
    }

    public function isStudent()
    {
        return $this->role === 'user';
    }

    // Check if student has rated a property
    public function hasRated($propertyId)
    {
        return $this->ratings()->where('property_id', $propertyId)->exists();
    }

    // Get user's rating for a specific property
    public function getRatingFor($propertyId)
    {
        return $this->ratings()->where('property_id', $propertyId)->first();
    }
}