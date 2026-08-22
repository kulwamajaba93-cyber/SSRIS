<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interaction extends Model
{
    protected $fillable = [
        'student_id',
        'supervisor_id',
        'proposal_id',
        'feedback_id',
        'student_progress_id',
        'action_type',
        'notes',
        'status',
        'document_reference',
    ];

    // Action type constants
    const ACTION_SUBMISSION = 'submission';
    const ACTION_FEEDBACK = 'feedback';
    const ACTION_APPROVAL = 'approval';
    const ACTION_REVISION_REQUEST = 'revision_request';

    // Status constants
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_REVISION_REQUIRED = 'revision_required';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get the student that owns the interaction.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the supervisor that owns the interaction.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the proposal associated with the interaction.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the feedback associated with the interaction.
     */
    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    /**
     * Get the student progress associated with the interaction.
     */
    public function studentProgress(): BelongsTo
    {
        return $this->belongsTo(StudentProgress::class);
    }

    /**
     * Get the meetings for the interaction.
     */
    public function meetings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_IN_REVIEW => 'info',
            self::STATUS_REVISION_REQUIRED => 'warning',
            self::STATUS_COMPLETED => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_IN_REVIEW => 'In Review',
            self::STATUS_REVISION_REQUIRED => 'Revision Required',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }

    /**
     * Get action type label.
     */
    public function getActionTypeLabelAttribute(): string
    {
        return match($this->action_type) {
            self::ACTION_SUBMISSION => 'Submission',
            self::ACTION_FEEDBACK => 'Feedback',
            self::ACTION_APPROVAL => 'Approval',
            self::ACTION_REVISION_REQUEST => 'Revision Request',
            default => 'Unknown',
        };
    }

    /**
     * Get response turnaround time (in days) since last student submission.
     */
    public function getTurnaroundDaysAttribute(): ?int
    {
        if ($this->action_type === self::ACTION_FEEDBACK || $this->action_type === self::ACTION_APPROVAL) {
            $lastSubmission = self::where('student_id', $this->student_id)
                ->where('supervisor_id', $this->supervisor_id)
                ->where('action_type', self::ACTION_SUBMISSION)
                ->where('created_at', '<', $this->created_at)
                ->latest()
                ->first();

            if ($lastSubmission) {
                return $lastSubmission->created_at->diffInDays($this->created_at);
            }
        }

        return null;
    }

    /**
     * Check if turnaround time is over 7 days (warning).
     */
    public function isOverdue(): bool
    {
        $turnaround = $this->turnaround_days;
        return $turnaround !== null && $turnaround > 7;
    }
}
