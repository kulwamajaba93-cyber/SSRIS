<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class UserManagementController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        // Middleware is applied in routes/web.php
    }

    /**
     * Display all users
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::query();

        // Search by name, programme, or registration number
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Show form to create a new user
     */
    public function create()
    {
        $supervisors = User::where('role', 'supervisor')->get();
        return view('admin.users.create', compact('supervisors'));
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'role' => ['required', Rule::in(['admin', 'supervisor', 'student'])],
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:255',
                'supervisor_id' => 'nullable|exists:ssris_users,id',
            ];

            // Add role-specific validation
            if ($request->role === 'student') {
                $rules['registration_number'] = 'required|string|max:50';
            } else {
                $rules['email'] = ['required', 'email', Rule::unique(User::class, 'email')];
                $rules['password'] = 'required|string|min:6';
            }

            $request->validate($rules);

            $user = match($request->role) {
                'student' => User::createStudent(
                    $request->name,
                    $request->registration_number,
                    $request->phone,
                    $request->supervisor_id
                ),
                'supervisor' => User::createSupervisor(
                    $request->name,
                    $request->email,
                    $request->password,
                    $request->phone,
                    $request->department
                ),
                'admin' => User::createAdmin(
                    $request->name,
                    $request->email,
                    $request->password
                ),
            };

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$user->name}' created successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit user
     */
    public function edit(User $user)
    {
        $supervisors = User::where('role', 'supervisor')->get();
        return view('admin.users.edit', compact('user', 'supervisors'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'role' => ['required', Rule::in(['admin', 'supervisor', 'student'])],
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:255',
                'supervisor_id' => 'nullable|exists:ssris_users,id',
            ];

            // Add role-specific validation
            if ($request->role === 'student') {
                $rules['registration_number'] = 'required|string|max:50';
            } else {
                $rules['email'] = ['required', 'email', Rule::unique(User::class, 'email')->ignore($user->id)];
                $rules['password'] = 'nullable|string|min:6';
            }

            $request->validate($rules);

            // Update user based on role
            if ($request->role === 'student') {
                // Parse registration number to extract components
                $parts = explode('/', $request->registration_number);
                $program = $parts[1] ?? '';
                $number = $parts[2] ?? '';
                $year = $parts[3] ?? '';

                $user->update([
                    'name' => $request->name,
                    'username' => $request->registration_number,
                    'program' => $program,
                    'reg_number' => "{$number}/{$year}",
                    'year' => $year,
                    'phone' => $request->phone,
                    'supervisor_id' => $request->supervisor_id,
                ]);
            } else {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'department' => $request->department,
                ]);

                // Update password if provided
                if ($request->password) {
                    $user->update([
                        'password' => Hash::make($request->password),
                    ]);
                }
            }

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$user->name}' updated successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'reset_type' => 'required|in:auto,manual',
            'password' => 'required_if:reset_type,manual|string|min:6',
        ]);

        if ($request->reset_type === 'auto') {
            // Automatic reset based on role
            if ($user->role === 'student') {
                // Regenerate from registration number
                $parts = explode('/', $user->username);
                if (count($parts) >= 4) {
                    $program = $parts[1] ?? '';
                    $number = $parts[2] ?? '';
                    $year = $parts[3] ?? '';
                    $password = 'mocu.' . strtolower($program) . ".{$number}.{$year}";
                } else {
                    return back()->with('error', 'Invalid registration number format for password generation');
                }
            } elseif ($user->role === 'supervisor') {
                // Default password for supervisors
                $password = 'password123';
            } else {
                // Default password for admin
                $password = 'admin123';
            }
        } else {
            // Manual reset
            $password = $request->password;
        }

        $user->update([
            'password' => Hash::make($password),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Password for '{$user->name}' has been reset successfully.");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' has been deleted successfully.");
    }

    /**
     * Show approve supervisors page
     */
    public function approveSupervisors()
    {
        $pendingSupervisors = User::where('role', 'supervisor')
            ->where('is_approved', false)
            ->get();

        return view('admin.supervisors.approve', compact('pendingSupervisors'));
    }

    /**
     * Approve supervisor
     */
    public function approveSupervisor(User $user)
    {
        if ($user->role !== 'supervisor') {
            return back()->with('error', 'Only supervisors can be approved.');
        }

        $user->update(['is_approved' => true]);

        return back()->with('success', "Supervisor '{$user->name}' has been approved successfully.");
    }

    /**
     * Reject supervisor
     */
    public function rejectSupervisor(User $user)
    {
        if ($user->role !== 'supervisor') {
            return back()->with('error', 'Only supervisors can be rejected.');
        }

        // Unassign all students from this supervisor
        User::where('role', 'student')->where('supervisor_id', $user->id)->update(['supervisor_id' => null]);

        $user->delete();

        return back()->with('success', "Supervisor '{$user->name}' has been rejected and removed. All assigned students have been unassigned.");
    }

    /**
     * Show bulk student creation form
     */
    public function bulkCreate()
    {
        $supervisors = User::where('role', 'supervisor')->where('is_approved', true)->get();
        return view('admin.users.bulk-create', compact('supervisors'));
    }

    /**
     * Store bulk students
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.program' => 'required|in:BBICT,BHRM,BAT',
            'students.*.reg_number' => 'required|string|max:20',
            'students.*.year' => 'required|string|max:10',
            'students.*.phone' => 'nullable|string|max:20',
            'supervisor_id' => 'nullable|exists:ssris_users,id',
        ]);

        $createdCount = 0;
        foreach ($request->students as $studentData) {
            try {
                // Generate full registration number
                $registrationNumber = "MOCU/{$studentData['program']}/{$studentData['reg_number']}/{$studentData['year']}";

                User::createStudent(
                    $studentData['name'],
                    $registrationNumber,
                    $studentData['phone'] ?? null,
                    $request->supervisor_id
                );
                $createdCount++;
            } catch (\Exception $e) {
                continue; // Skip duplicates and continue
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', "{$createdCount} students created successfully.");
    }

    /**
     * Show CSV import form
     */
    public function importCSV()
    {
        return view('admin.users.import-csv');
    }

    /**
     * Process Excel import
     */
    public function processCSVImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('csv_file'));

            $message = "Import completed: {$import->results['success']} students created successfully.";
            
            if ($import->results['failed'] > 0) {
                $message .= " {$import->results['failed']} rows failed. {$import->results['duplicates']} duplicates skipped.";
            }

            if (count($import->results['errors']) > 0) {
                return redirect()->route('admin.users.import-csv')
                    ->with('warning', $message)
                    ->with('importErrors', $import->results['errors']);
            }

            return redirect()->route('admin.users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.users.import-csv')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Show supervisor assignment form
     */
    public function assignSupervisor()
    {
        $students = User::where('role', 'student')->whereNull('supervisor_id')->get();
        $supervisors = User::where('role', 'supervisor')->where('is_approved', true)->get();

        return view('admin.users.assign-supervisor', compact('students', 'supervisors'));
    }

    /**
     * Store supervisor assignment
     */
    public function storeAssignment(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:ssris_users,id',
            'supervisor_id' => 'required|exists:ssris_users,id',
        ]);

        $assignedCount = User::where('role', 'student')
            ->whereIn('id', $request->student_ids)
            ->update(['supervisor_id' => $request->supervisor_id]);

        return redirect()->route('admin.users.index')
            ->with('success', "{$assignedCount} students assigned to supervisor successfully.");
    }
}
