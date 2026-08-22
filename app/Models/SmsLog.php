<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message',
        'status',
        'type',
        'user_id',
        'proposal_id',
        'meeting_id',
        'api_response',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user associated with the SMS log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the proposal associated with the SMS log.
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the meeting associated with the SMS log.
     */
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
