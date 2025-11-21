<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationLog extends Model
{
    use HasFactory;

    protected $table = 'recommendation_logs';
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'property_id',
        'recommendation_score',
        'recommendation_type',
        'was_clicked',
        'was_rated',
    ];

    protected $casts = [
        'recommendation_score' => 'decimal:2',
        'was_clicked' => 'boolean',
        'was_rated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }

    // Scopes
    public function scopeCollaborative($query)
    {
        return $query->where('recommendation_type', 'Collaborative');
    }

    public function scopeContentBased($query)
    {
        return $query->where('recommendation_type', 'Content-Based');
    }

    public function scopeHybrid($query)
    {
        return $query->where('recommendation_type', 'Hybrid');
    }
}
