<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\InteractionTrackingController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\FeedbackController;
use App\Http\Controllers\Supervisor\MeetingController;
use App\Http\Controllers\Supervisor\ResearchStageController;
use App\Http\Controllers\Supervisor\MessageController as SupervisorMessageController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\FeedbackController as StudentFeedbackController;
use App\Http\Controllers\Student\DocumentController;
use App\Http\Controllers\Student\MessageController as StudentMessageController;
use App\Http\Controllers\Student\MeetingController as StudentMeetingController;
use App\Http\Controllers\MessageNotificationController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Message notifications (student & supervisor)
Route::middleware('auth')->group(function () {
    Route::get('/messages/unread-count', [MessageNotificationController::class, 'unreadCount'])
        ->name('messages.unread-count');
    Route::get('/messages/poll', [MessageNotificationController::class, 'poll'])
        ->name('messages.poll');
    Route::get('/messages/poll/{student}', [MessageNotificationController::class, 'poll'])
        ->name('messages.poll.student');
});

// Admin routes (protected)
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/assign-supervisor', [UserManagementController::class, 'assignSupervisor'])->name('assign-supervisor');
    Route::post('/assign-supervisor', [UserManagementController::class, 'storeAssignment'])->name('assign-supervisor.store');
    Route::get('/import-csv', [UserManagementController::class, 'importCSV'])->name('users.import-csv');
    Route::post('/import-csv', [UserManagementController::class, 'processCSVImport'])->name('users.process-csv');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/supervisors/approve', [UserManagementController::class, 'approveSupervisors'])->name('supervisors.approve.index');
    Route::post('/supervisors/performance/approve', [AdminDashboardController::class, 'approvePerformance'])->name('supervisors.performance.approve');
    Route::post('/supervisors/performance/reject', [AdminDashboardController::class, 'rejectPerformance'])->name('supervisors.performance.reject');
    Route::post('/supervisors/{user}/approve', [UserManagementController::class, 'approveSupervisor'])->name('supervisors.approve');
    Route::post('/supervisors/{user}/reject', [UserManagementController::class, 'rejectSupervisor'])->name('supervisors.reject');
    Route::get('/interactions', [\App\Http\Controllers\Admin\InteractionController::class, 'index'])->name('interactions.index');
    Route::get('/interactions/{student}', [\App\Http\Controllers\Admin\InteractionController::class, 'show'])->name('interactions.show');
    Route::get('/interaction-tracking', [InteractionTrackingController::class, 'index'])->name('interaction-tracking.index');
    Route::get('/interaction-tracking/{student}', [InteractionTrackingController::class, 'show'])->name('interaction-tracking.show');
});

// Supervisor routes (protected)
Route::prefix('supervisor')->middleware(['auth', 'supervisor'])->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/research-progress', [\App\Http\Controllers\Supervisor\ResearchProgressController::class, 'index'])->name('research-progress.index');
    Route::get('/students/{student}/research-progress', [\App\Http\Controllers\Supervisor\ResearchProgressController::class, 'edit'])->name('research-progress.edit');
    Route::get('/students', [SupervisorDashboardController::class, 'students'])->name('students.index');
    Route::get('/proposals', [SupervisorDashboardController::class, 'proposals'])->name('proposals.index');
    Route::get('/proposals/{proposal}/download', [SupervisorDashboardController::class, 'downloadProposal'])->name('proposals.download');
    Route::get('/meetings', [SupervisorDashboardController::class, 'meetings'])->name('meetings.index');
    Route::get('/proposals/{proposal}/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/proposals/{proposal}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::put('/feedback/{feedback}/status', [FeedbackController::class, 'updateStatus'])->name('feedback.update-status');
    Route::get('/meetings/create', [MeetingController::class, 'create'])->name('meetings.create');
    Route::post('/meetings', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');
    Route::get('/meetings/{meeting}/edit', [MeetingController::class, 'edit'])->name('meetings.edit');
    Route::put('/meetings/{meeting}', [MeetingController::class, 'update'])->name('meetings.update');
    Route::get('/messages', [SupervisorMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{student}', [SupervisorMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{student}', [SupervisorMessageController::class, 'store'])->name('messages.store');
    Route::get('/interactions', [\App\Http\Controllers\Supervisor\InteractionController::class, 'index'])->name('interactions.index');
    Route::get('/interactions/{student}', [\App\Http\Controllers\Supervisor\InteractionController::class, 'show'])->name('interactions.show');
});

// Student routes (protected)
Route::prefix('student')->middleware(['auth', 'student'])->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/research-progress', [\App\Http\Controllers\Student\ResearchProgressController::class, 'index'])->name('research-progress.index');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/meetings', [StudentMeetingController::class, 'index'])->name('meetings.index');
    Route::get('/meetings/{meeting}', [StudentMeetingController::class, 'show'])->name('meetings.show');
    Route::get('/feedback', [StudentFeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/{feedback}', [StudentFeedbackController::class, 'show'])->name('feedback.show');
    Route::get('/messages', [StudentMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [StudentMessageController::class, 'store'])->name('messages.store');
    Route::get('/interactions', [\App\Http\Controllers\Student\InteractionController::class, 'index'])->name('interactions.index');
});

// Test route for authentication debugging
Route::get('/test-auth', function () {
    $results = [];
    
    // Test Admin
    $admin = User::where('email', 'admin@mocu.ac.tz')->first();
    if ($admin) {
        $results['admin'] = [
            'found' => true,
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => $admin->role,
            'password_check' => Hash::check('admin123', $admin->password)
        ];
    } else {
        $results['admin'] = ['found' => false];
    }
    
    // Test Supervisor
    $supervisor = User::where('email', 'supervisor@mocu.ac.tz')->first();
    if ($supervisor) {
        $results['supervisor'] = [
            'found' => true,
            'name' => $supervisor->name,
            'email' => $supervisor->email,
            'role' => $supervisor->role,
            'password_check' => Hash::check('supervisor123', $supervisor->password)
        ];
    } else {
        $results['supervisor'] = ['found' => false];
    }
    
    // Test Student
    $student = User::where('username', 'MOCU/BBICT/1089/23')->first();
    if ($student) {
        $results['student'] = [
            'found' => true,
            'name' => $student->name,
            'username' => $student->username,
            'role' => $student->role,
            'program' => $student->program,
            'password_check' => Hash::check('mocu.bbict.1089.23', $student->password)
        ];
    } else {
        $results['student'] = ['found' => false];
    }
    
    return response()->json($results);
});
