<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landlord extends Model
{
    use HasFactory;

    protected $table = 'landlords';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'phone',
        'company_name',
        'business_address', // Added this field
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    // Accessors
    public function getTotalPropertiesAttribute()
    {
        return $this->properties()->count();
    }

    public function getActivePropertiesAttribute()
    {
        return $this->properties()->where('is_active', true)->count();
    }
}