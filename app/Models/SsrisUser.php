<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SsrisUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'ssris_users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'role',
        'password',
        'program',
        'reg_number',
        'year',
        'supervisor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_STUDENT = 'student';

    // Program constants
    const PROGRAM_BBICT = 'BBICT';
    const PROGRAM_BHRM = 'BHRM';
    const PROGRAM_BAT = 'BAT';

    /**
     * Get the supervisor that oversees the student.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(SsrisUser::class, 'supervisor_id');
    }

    /**
     * Get the students assigned to this supervisor.
     */
    public function students(): HasMany
    {
        return $this->hasMany(SsrisUser::class, 'supervisor_id');
    }

    /**
     * Get the research projects for the student.
     */
    public function researchProjects(): HasMany
    {
        return $this->hasMany(ResearchProject::class, 'student_id');
    }

    /**
     * Get the supervised projects for the supervisor.
     */
    public function supervisedProjects(): HasMany
    {
        return $this->hasMany(ResearchProject::class, 'supervisor_id');
    }

    /**
     * Get the proposals submitted by the student.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'student_id');
    }

    /**
     * Get the meetings for the student.
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'student_id');
    }

    /**
     * Get the supervised meetings for the supervisor.
     */
    public function supervisedMeetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'supervisor_id');
    }

    /**
     * Get the feedback received by the student.
     */
    public function receivedFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'student_id');
    }

    /**
     * Get the feedback given by the supervisor.
     */
    public function givenFeedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'supervisor_id');
    }

    /**
     * Get the current research project for the student.
     */
    public function currentProject()
    {
        return $this->researchProjects()->first();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === self::ROLE_SUPERVISOR;
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Generate student username from registration details
     */
    public static function generateStudentUsername(string $program, string $regNumber, string $year): string
    {
        return "MOCU/{$program}/{$regNumber}/{$year}";
    }

    /**
     * Generate student password from registration details
     */
    public static function generateStudentPassword(string $program, string $regNumber, string $year): string
    {
        return "mocu." . strtolower($program) . ".{$regNumber}.{$year}";
    }

    /**
     * Get login field based on user role
     */
    public function getLoginField(): string
    {
        return $this->isStudent() ? 'username' : 'email';
    }

    /**
     * Get login value
     */
    public function getLoginValue(): string
    {
        return $this->isStudent() ? $this->username : $this->email;
    }

    /**
     * Scope to get users by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get students by program
     */
    public function scopeByProgram($query, string $program)
    {
        return $query->where('program', $program);
    }

    /**
     * Scope to get students by year
     */
    public function scopeByYear($query, string $year)
    {
        return $query->where('year', $year);
    }
}
