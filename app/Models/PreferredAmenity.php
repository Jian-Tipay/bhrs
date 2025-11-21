<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreferredAmenity extends Model
{
    protected $table = 'preferred_amenities';
    protected $primaryKey = 'pref_amenity_id';
    
    protected $fillable = [
        'preference_id',
        'amenity_id'
    ];

    public function preference()
    {
        return $this->belongsTo(StudentPreference::class, 'preference_id', 'preference_id');
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class, 'amenity_id', 'amenity_id');
    }
}