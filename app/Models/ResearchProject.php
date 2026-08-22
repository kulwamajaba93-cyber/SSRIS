<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchProject extends Model
{
    protected $fillable = [
        'title',
        'description',
        'student_id',
        'supervisor_id',
        'status',
        'start_date',
        'expected_completion_date',
        'actual_completion_date',
        'research_area',
        'keywords',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date' => 'date',
    ];

    // Status constants
    const STATUS_PROPOSAL_SUBMITTED = 'proposal_submitted';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REVISION = 'revision';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get the student that owns the project.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the supervisor that oversees the project.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the proposals for the project.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'project_id');
    }

    /**
     * Get the meetings for the project.
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'project_id');
    }

    /**
     * Get the feedback for the project.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'project_id');
    }

    /**
     * Get the latest proposal for the project.
     */
    public function latestProposal()
    {
        return $this->proposals()->latest()->first();
    }

    /**
     * Get completed meetings count.
     */
    public function getCompletedMeetingsCountAttribute(): int
    {
        return $this->meetings()->where('status', 'completed')->count();
    }

    /**
     * Get total proposals count.
     */
    public function getTotalProposalsCountAttribute(): int
    {
        return $this->proposals()->count();
    }

    /**
     * Get pending feedback count.
     */
    public function getPendingFeedbackCountAttribute(): int
    {
        return $this->feedback()->where('status', 'pending')->count();
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PROPOSAL_SUBMITTED => 'secondary',
            self::STATUS_UNDER_REVIEW => 'info',
            self::STATUS_REVISION => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_IN_PROGRESS => 'primary',
            self::STATUS_COMPLETED => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PROPOSAL_SUBMITTED => 'Proposal Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_REVISION => 'Revision Required',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }
}
