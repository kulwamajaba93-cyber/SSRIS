<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'project_id',
        'student_id',
        'supervisor_id',
        'proposal_id',
        'meeting_id',
        'type',
        'title',
        'comments',
        'priority',
        'status',
        'action_required',
        'due_date',
        'addressed_date',
        'student_response',
    ];

    protected $casts = [
        'due_date' => 'date',
        'addressed_date' => 'date',
    ];

    // Type constants
    const TYPE_PROPOSAL_FEEDBACK = 'proposal_feedback';
    const TYPE_MEETING_FEEDBACK = 'meeting_feedback';
    const TYPE_GENERAL_FEEDBACK = 'general_feedback';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ADDRESSED = 'addressed';
    const STATUS_RESOLVED = 'resolved';

    /**
     * Get the project that owns the feedback.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    /**
     * Get the student that owns the feedback.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the supervisor that provided the feedback.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the proposal associated with the feedback.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    /**
     * Get the meeting associated with the feedback.
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityBadgeColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'secondary',
            self::PRIORITY_MEDIUM => 'info',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_URGENT => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_ADDRESSED => 'info',
            self::STATUS_RESOLVED => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
            default => 'Unknown',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ADDRESSED => 'Addressed',
            self::STATUS_RESOLVED => 'Resolved',
            default => 'Unknown',
        };
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_PROPOSAL_FEEDBACK => 'Proposal Feedback',
            self::TYPE_MEETING_FEEDBACK => 'Meeting Feedback',
            self::TYPE_GENERAL_FEEDBACK => 'General Feedback',
            default => 'Unknown',
        };
    }

    /**
     * Check if feedback is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date < now() && $this->status !== self::STATUS_RESOLVED;
    }

    /**
     * Get days until due.
     */
    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get related entity name (proposal or meeting).
     */
    public function getRelatedEntityNameAttribute(): string
    {
        if ($this->proposal) {
            return "Proposal {$this->proposal->version_display}";
        }
        
        if ($this->meeting) {
            return "Meeting: {$this->meeting->title}";
        }
        
        return 'General';
    }
}
