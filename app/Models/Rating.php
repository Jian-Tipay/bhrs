<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';
    protected $primaryKey = 'rating_id';

    protected $fillable = [
        'user_id',
        'property_id',
        'rating',
        'review_text',
        'landlord_reply',
        'replied_at',
    ];

    /**
     * Cast attributes to native types
     */
    protected $casts = [
        'rating' => 'decimal:1',
        'replied_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who left the review
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the property being reviewed
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}