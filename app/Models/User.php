<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ssris_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
        'is_approved',
        'department',
        'performance_approval_status',
        'performance_signed_at',
        'performance_hod_remarks',
        'performance_approved_by',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'performance_signed_at' => 'datetime',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a HOD (Head of Department)
     */
    public function isHod(): bool
    {
        return $this->role === 'hod';
    }

    /**
     * Check if user is a supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    /**
     * Check if supervisor is approved
     */
    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get the supervisor assigned to this student
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the students assigned to this supervisor
     */
    public function assignedStudents()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Get the student progress entries for this user
     */
    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class, 'student_id');
    }

    /**
     * Get the current active progress entry for this student
     */
    public function currentProgress()
    {
        return $this->hasOne(StudentProgress::class, 'student_id')
            ->where('status', 'pending')
            ->orWhere('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->latestOfMany();
    }

    /**
     * Get proposals for this user
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'student_id');
    }

    /**
     * Get feedback received by this user
     */
    public function feedbackReceived()
    {
        return $this->hasMany(Feedback::class, 'student_id');
    }

    /**
     * Get feedback given by this user
     */
    public function feedbackGiven()
    {
        return $this->hasMany(Feedback::class, 'supervisor_id');
    }

    /**
     * Get meetings for this user (backwards compatibility).
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'student_id');
    }

    /**
     * Get meetings that this user is part of (many-to-many).
     */
    public function meetingParticipations()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_student', 'user_id', 'meeting_id');
    }

    /**
     * Get the HOD who approved this supervisor's performance
     */
    public function performanceApprovedBy()
    {
        return $this->belongsTo(User::class, 'performance_approved_by');
    }

    /**
     * Check if supervisor performance is approved
     */
    public function isPerformanceApproved(): bool
    {
        return $this->performance_approval_status === 'approved';
    }

    /**
     * Check if supervisor performance is rejected
     */
    public function isPerformanceRejected(): bool
    {
        return $this->performance_approval_status === 'rejected';
    }

    /**
     * Check if supervisor performance is pending
     */
    public function isPerformancePending(): bool
    {
        return $this->performance_approval_status === 'pending' || $this->performance_approval_status === null;
    }

    /**
     * Completion rate: percentage of assigned students with an approved final report.
     */
    public function getCompletionRate(): float
    {
        $assignedCount = $this->assignedStudents()->count();

        if ($assignedCount === 0) {
            return 0;
        }

        $completedStudents = Proposal::whereIn('student_id', $this->assignedStudents()->pluck('id'))
            ->where('document_type', 'report')
            ->where('status', 'approved')
            ->distinct('student_id')
            ->count('student_id');

        return round(($completedStudents / $assignedCount) * 100, 1);
    }

    /**
     * Check if all assigned students have completed research (100% completion).
     */
    public function hasFullCompletion(): bool
    {
        return $this->getCompletionRate() >= 100;
    }

    /**
     * Performance can only be approved when all assigned students are complete (100%).
     */
    public function canApprovePerformance(): bool
    {
        return $this->isSupervisor()
            && $this->assignedStudents()->count() > 0
            && $this->getCompletionRate() >= 100;
    }

    /**
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    
    /**
     * Get SMS logs associated with this user
     */
    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class, 'user_id');
    }

    /**
     * Get unread messages for this user
     */
    public function unreadMessages()
    {
        return $this->receivedMessages()->where('read', false);
    }

    /**
     * Get interactions where this user is the student
     */
    public function interactionsAsStudent()
    {
        return $this->hasMany(Interaction::class, 'student_id');
    }

    /**
     * Get interactions where this user is the supervisor.
     */
    public function interactionsAsSupervisor()
    {
        return $this->hasMany(Interaction::class, 'supervisor_id');
    }

    /**
     * Get meetings where this user is the supervisor.
     */
    public function supervisedMeetings()
    {
        return $this->hasMany(Meeting::class, 'supervisor_id');
    }

    /**
     * Get the research projects for this user (as a student).
     */
    public function researchProjects()
    {
        return $this->hasMany(ResearchProject::class, 'student_id');
    }

    /**
     * Get the supervised research projects (as a supervisor).
     */
    public function supervisedResearchProjects()
    {
        return $this->hasMany(ResearchProject::class, 'supervisor_id');
    }

    /**
     * Total unread messages received (for notification badge).
     */
    public function unreadMessagesCount(): int
    {
        return $this->unreadMessages()->count();
    }

    /**
     * Generate student username based on registration details
     */
    public static function generateStudentUsername($program, $regNumber, $year): string
    {
        return "MOCU/{$program}/{$regNumber}/{$year}";
    }

    /**
     * Generate student password based on registration details
     */
    public static function generateStudentPassword($program, $regNumber, $year): string
    {
        return "mocu." . strtolower($program) . ".{$regNumber}.{$year}";
    }

    /**
     * Create a new student with auto-generated credentials
     * registrationNumber should be the full format: e.g., "MOCU/BBICT/1089/23"
     */
    public static function createStudent($name, $registrationNumber, $phone = null, $supervisorId = null)
    {
        // Parse registration number to extract components
        // Format: MOCU/BBICT/1089/23
        $parts = explode('/', $registrationNumber);
        
        if (count($parts) < 4) {
            throw new \InvalidArgumentException('Invalid registration number format. Expected: MOCU/PROGRAM/NUMBER/YEAR');
        }

        $program = $parts[1] ?? '';
        $number = $parts[2] ?? '';
        $year = $parts[3] ?? '';

        $password = self::generateStudentPassword($program, $number, $year);

        return self::create([
            'name' => $name,
            'username' => $registrationNumber,
            'email' => null,
            'role' => 'student',
            'password' => Hash::make($password),
            'program' => $program,
            'reg_number' => "{$number}/{$year}",
            'year' => $year,
            'phone' => $phone,
            'supervisor_id' => $supervisorId,
        ]);
    }

    /**
     * Create a new supervisor
     */
    public static function createSupervisor($name, $email, $password, $phone = null, $department = null)
    {
        return self::create([
            'name' => $name,
            'username' => null,
            'email' => $email,
            'role' => 'supervisor',
            'password' => Hash::make($password),
            'program' => null,
            'reg_number' => null,
            'year' => null,
            'phone' => $phone,
            'department' => $department,
            'supervisor_id' => null,
            'is_approved' => true,
        ]);
    }

    /**
     * Create a new admin
     */
    public static function createAdmin($name, $email, $password)
    {
        return self::create([
            'name' => $name,
            'username' => null,
            'email' => $email,
            'role' => 'admin',
            'password' => Hash::make($password),
            'program' => null,
            'reg_number' => null,
            'year' => null,
            'supervisor_id' => null,
        ]);
    }

    /**
     * Get dashboard route based on user role
     */
    public function getDashboardRoute(): string
    {
        return match($this->role) {
            'admin' => route('admin.dashboard'),
            'supervisor' => route('supervisor.dashboard'),
            'student' => route('student.dashboard'),
            default => route('login')
        };
    }

    /**
     * Get login field (username for students, email for others)
     */
    public function getLoginField(): string
    {
        return $this->isStudent() ? 'username' : 'email';
    }

    /**
     * Get login identifier (username for students, email for others)
     */
    public function getLoginIdentifier(): string
    {
        return $this->isStudent() ? $this->username : $this->email;
    }

    /**
     * Scope to get only admins
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope to get only supervisors
     */
    public function scopeSupervisors($query)
    {
        return $query->where('role', 'supervisor');
    }

    /**
     * Scope to get only pending supervisors
     */
    public function scopePendingSupervisors($query)
    {
        return $query->where('role', 'supervisor')->where('is_approved', false);
    }

    /**
     * Scope to get only approved supervisors
     */
    public function scopeApprovedSupervisors($query)
    {
        return $query->where('role', 'supervisor')->where('is_approved', true);
    }

    /**
     * Scope to get only students
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope to get students by program
     */
    public function scopeByProgram($query, $program)
    {
        return $query->where('program', $program);
    }

    /**
     * Scope to get students by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            // Before deleting anything, handle all foreign key references that point to this user
            // Set reviewed_by to null for any proposals this user reviewed
            \App\Models\Proposal::where('reviewed_by', $user->id)->update(['reviewed_by' => null]);
            
            // Set user_id to null for any SMS logs linked to this user
            \App\Models\SmsLog::where('user_id', $user->id)->update(['user_id' => null]);
            
            // Set approved_by to null for any student progress records approved by this user
            \App\Models\StudentProgress::where('approved_by', $user->id)->update(['approved_by' => null]);
            
            // Set supervisor_id to null for any meetings supervised by this user
            \App\Models\Meeting::where('supervisor_id', $user->id)->update(['supervisor_id' => null]);

            // Delete all related records in the correct order (dependents first)
            // Feedback depends on proposals/meetings, so delete feedback first
            $user->feedbackReceived()->delete();
            $user->feedbackGiven()->delete();
            
            // Then delete proposals, meetings, student progress
            $user->studentProgress()->delete();
            $user->proposals()->delete();
            $user->meetings()->delete();
            $user->meetingParticipations()->detach();
            $user->sentMessages()->delete();
            $user->receivedMessages()->delete();
            $user->interactionsAsStudent()->delete();
            $user->interactionsAsSupervisor()->delete();
            $user->supervisedMeetings()->delete();
            $user->researchProjects()->delete();
            $user->supervisedResearchProjects()->delete();

            // If deleting a supervisor, set supervisor_id to null for assigned students
            if ($user->isSupervisor()) {
                $user->assignedStudents()->update(['supervisor_id' => null]);
            }

            // If deleting a user who approved someone's performance, set that to null
            User::where('performance_approved_by', $user->id)->update(['performance_approved_by' => null]);
        });
    }
}
