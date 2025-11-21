<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPreference extends Model
{
    protected $table = 'student_preferences';
    protected $primaryKey = 'preference_id';
    
    protected $fillable = [
        'user_id',
        'budget_min',
        'budget_max',
        'preferred_distance',
        'room_type',
        'gender_preference'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preferredAmenities()
    {
        return $this->hasMany(PreferredAmenity::class, 'preference_id', 'preference_id');
    }
}
