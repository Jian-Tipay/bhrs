<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';
    protected $primaryKey = 'amenity_id';

    protected $fillable = [
        'amenity_name',
    ];

    public function properties()
{
    return $this->belongsToMany(Property::class, 'property_amenities', 'amenity_id', 'property_id');
}

    public function preferences()
    {
        return $this->belongsToMany(
            StudentPreference::class,
            'preferred_amenities',
            'amenity_id',
            'preference_id',
            'amenity_id',
            'preference_id'
        );
    }
}