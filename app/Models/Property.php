<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';
    protected $primaryKey = 'id';

    protected $fillable = [
        'landlord_id',
        'title',
        'description',
        'address',
        'price',
        'rooms',
        'available',
        'image',
        'latitude',
        'longitude',
        'distance_from_campus',
        'monthly_rate_min',
        'monthly_rate_max',
        'accreditation_status',
        'accreditation_date',
        'capacity',
        'available_slots',
        'house_rules',
        'owner_name',
        'owner_contact',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'distance_from_campus' => 'decimal:2',
        'monthly_rate_min' => 'decimal:2',
        'monthly_rate_max' => 'decimal:2',
        'available' => 'boolean',
        'is_active' => 'boolean',
        'accreditation_date' => 'date',
        'capacity' => 'integer',
        'available_slots' => 'integer',
        'rooms' => 'integer',
    ];

    /**
     * Get the landlord that owns the property
     */
    public function landlord()
    {
       return $this->belongsTo(Landlord::class);
    }

    /**
     * Get all images for the property
     */
    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id', 'id');
    }

    /**
     * Get all amenities for the property (many-to-many)
     */
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities', 'property_id', 'amenity_id');
    }

    /**
     * Get all property amenity pivot records (one-to-many)
     * FIXED: Changed third parameter from 'property_id' to 'id'
     */
    public function propertyAmenities()
    {
        return $this->hasMany(PropertyAmenity::class, 'property_id', 'id');
    }
    
    /**
     * Get all bookings for the property
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'property_id', 'id');
    }

    /**
     * Get all ratings for the property
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'property_id', 'id');
    }

    /**
     * Get all views for the property
     */
    public function views()
    {
        return $this->hasMany(PropertyView::class, 'property_id', 'id');
    }

    /**
     * Scope to get only active properties
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope to get only available properties
     */
    public function scopeAvailable($query)
    {
        return $query->where('available', 1);
    }

    /**
     * Get average rating for the property
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get total ratings count
     */
    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }
    public function hasImage()
{
    return !empty($this->image) && file_exists(public_path('assets/img/boarding/' . $this->image));
}

/**
 * Get the image URL or default
 */
public function getImageUrlAttribute()
{
    if ($this->hasImage()) {
        return asset('assets/img/boarding/' . $this->image);
    }
    return asset('assets/img/boarding/default.jpg');
}

/**
 * Get the full image path
 */
public function getImagePathAttribute()
{
    return $this->image ? public_path('assets/img/boarding/' . $this->image) : null;
}
}