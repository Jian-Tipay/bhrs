<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAmenity extends Model
{
    protected $table = 'property_amenities';
    protected $primaryKey = 'property_amenity_id';
    
    protected $fillable = [
        'property_id',
        'amenity_id'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function amenity()
    {
        return $this->belongsTo(Amenity::class, 'amenity_id', 'amenity_id');
    }
}
