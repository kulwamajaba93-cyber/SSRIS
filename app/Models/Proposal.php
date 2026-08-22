<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    protected $fillable = [
        'project_id',
        'student_id',
        'title',
        'abstract',
        'file_path',
        'original_filename',
        'version',
        'document_type',
        'status',
        'submission_notes',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_comments',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISION = 'revision';

    /**
     * Get the project that owns the proposal.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    /**
     * Get the student that owns the proposal.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the reviewer (supervisor) that reviewed the proposal.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the feedback for the proposal.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'proposal_id');
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'secondary',
            self::STATUS_UNDER_REVIEW => 'info',
            self::STATUS_REVIEWED => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_REVISION => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_REVIEWED => 'Reviewed',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_REVISION => 'Revision Required',
            default => 'Unknown',
        };
    }

    /**
     * Get version display format.
     */
    public function getVersionDisplayAttribute(): string
    {
        return "v{$this->version}";
    }

    /**
     * Get document type label.
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'concept_notes' => 'Concept Notes',
            'proposal' => 'Proposal',
            'data_collection' => 'Data Collection & Analysis',
            'report' => 'Report',
            default => 'Document',
        };
    }

    /**
     * Map document type to research stage step number.
     */
    public static function getStepNumberForDocumentType(string $documentType): ?int
    {
        return match($documentType) {
            'concept_notes' => 1,
            'proposal' => 2,
            'data_collection' => 3,
            'report' => 4,
            default => null,
        };
    }

    /**
     * Check if proposal has been reviewed.
     */
    public function isReviewed(): bool
    {
        return in_array($this->status, [self::STATUS_REVIEWED, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_REVISION]);
    }

    /**
     * Get file size in human readable format.
     */
    public function getFileSizeAttribute(): string
    {
        if (!$this->file_path || !file_exists(storage_path('app/' . $this->file_path))) {
            return 'N/A';
        }

        $bytes = filesize(storage_path('app/' . $this->file_path));
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
