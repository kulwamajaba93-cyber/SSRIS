<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'stage_id',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Relationship to student (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship to research stage
    public function stage()
    {
        return $this->belongsTo(ResearchStage::class);
    }

    // Relationship to approving supervisor (User)
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
