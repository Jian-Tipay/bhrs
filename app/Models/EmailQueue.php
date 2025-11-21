<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    protected $table = 'email_queue';
    protected $primaryKey = 'queue_id';
    protected $fillable = [
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'status',
        'attempts',
        'last_attempt_at',
        'sent_at',
        'error_message',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
