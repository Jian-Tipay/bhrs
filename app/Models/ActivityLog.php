<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'subject_type',
        'subject_id',
        'subject_name',
        'description',
        'ip_address',
    ];

    public $timestamps = true;
}
