<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    protected $fillable = [
        'project_id',
        'student_id',
        'supervisor_id',
        'title',
        'description',
        'meeting_date',
        'status',
        'discussion_notes',
        'action_points',
        'supervisor_notes',
        'scheduled_at',
        'completed_at',
        'location',
        'agenda',
        'meeting_url',
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the project that owns the meeting.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    /**
     * Get the student that owns the meeting (for backwards compatibility).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the students for this meeting (many-to-many).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_student', 'meeting_id', 'user_id');
    }

    /**
     * Get the supervisor that owns the meeting.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the feedback for the meeting.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'meeting_id');
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Check if meeting is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if meeting is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_SCHEDULED && $this->meeting_date > now();
    }

    /**
     * Get meeting date formatted for display.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->meeting_date->format('M j, Y - g:i A');
    }

    /**
     * Get action points as array.
     */
    public function getActionPointsArrayAttribute(): array
    {
        return $this->action_points ? explode("\n", $this->action_points) : [];
    }

    /**
     * Get meeting URL with https:// prefix if not already present.
     */
    public function getMeetingUrlAttribute(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);
        if (!str_starts_with($value, 'http://') && !str_starts_with($value, 'https://')) {
            return 'https://' . $value;
        }

        return $value;
    }
}
