<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'students';
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'password',
        'program',
        'year_level',
        'gender',
        'contact_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'student_id', 'student_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'student_id', 'student_id');
    }

    public function preferences()
    {
        return $this->hasOne(StudentPreference::class, 'student_id', 'student_id');
    }

    public function views()
    {
        return $this->hasMany(HouseView::class, 'student_id', 'student_id');
    }

    public function recommendationLogs()
    {
        return $this->hasMany(RecommendationLog::class, 'student_id', 'student_id');
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Check if student has rated a boarding house
    public function hasRated($houseId)
    {
        return $this->ratings()->where('house_id', $houseId)->exists();
    }

    // Get student's rating for a specific house
    public function getRatingFor($houseId)
    {
        return $this->ratings()->where('house_id', $houseId)->first();
    }
}