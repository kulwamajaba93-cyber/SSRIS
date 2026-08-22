<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'step_number',
        'active',
    ];

    // Relationship to student progress
    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class);
    }
}
