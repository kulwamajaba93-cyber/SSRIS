<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SsrisUser;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'totalStudents' => SsrisUser::byRole(SsrisUser::ROLE_STUDENT)->count(),
            'totalSupervisors' => SsrisUser::byRole(SsrisUser::ROLE_SUPERVISOR)->count(),
            'bbictStudents' => SsrisUser::byRole(SsrisUser::ROLE_STUDENT)->byProgram(SsrisUser::PROGRAM_BBICT)->count(),
            'unassignedStudents' => SsrisUser::byRole(SsrisUser::ROLE_STUDENT)->whereNull('supervisor_id')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        $users = SsrisUser::orderBy('role')->orderBy('name')->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Show form to create user
     */
    public function createUser()
    {
        $supervisors = SsrisUser::byRole(SsrisUser::ROLE_SUPERVISOR)->get();
        return view('admin.create-user', compact('supervisors'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,supervisor,student',
            'email' => 'required_if:role,admin,supervisor|nullable|email|unique:ssris_users,email',
            'program' => 'required_if:role,student|nullable|in:BBICT,BHRM,BAT',
            'reg_number' => 'required_if:role,student|nullable|string|max:20',
            'year' => 'required_if:role,student|nullable|string|max:10',
            'supervisor_id' => 'nullable|exists:ssris_users,id',
        ]);

        $user = new SsrisUser();
        $user->name = $request->name;
        $user->role = $request->role;

        if ($request->role === SsrisUser::ROLE_STUDENT) {
            // Generate student username and password
            $username = SsrisUser::generateStudentUsername(
                $request->program,
                $request->reg_number,
                $request->year
            );
            $password = SsrisUser::generateStudentPassword(
                $request->program,
                $request->reg_number,
                $request->year
            );

            $user->username = $username;
            $user->program = $request->program;
            $user->reg_number = $request->reg_number;
            $user->year = $request->year;
            $user->supervisor_id = $request->supervisor_id;
            $user->password = Hash::make($password);
        } else {
            // For admin and supervisor
            $user->email = $request->email;
            $user->password = Hash::make('password123'); // Default password
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show form to edit user
     */
    public function editUser(SsrisUser $user)
    {
        $supervisors = SsrisUser::byRole(SsrisUser::ROLE_SUPERVISOR)->get();
        return view('admin.edit-user', compact('user', 'supervisors'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, SsrisUser $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required_if:role,admin,supervisor|nullable|email|unique:ssris_users,email,' . $user->id,
            'supervisor_id' => 'nullable|exists:ssris_users,id',
        ]);

        $user->name = $request->name;
        
        if ($user->role !== SsrisUser::ROLE_STUDENT) {
            $user->email = $request->email;
        }
        
        if ($user->role === SsrisUser::ROLE_STUDENT) {
            $user->supervisor_id = $request->supervisor_id;
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, SsrisUser $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Password reset successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser(SsrisUser $user)
    {
        // Prevent deletion of the current admin
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Assign supervisor to students
     */
    public function assignSupervisor()
    {
        $supervisors = SsrisUser::byRole(SsrisUser::ROLE_SUPERVISOR)->get();
        $unassignedStudents = SsrisUser::byRole(SsrisUser::ROLE_STUDENT)
            ->whereNull('supervisor_id')
            ->orderBy('program')
            ->orderBy('name')
            ->get();

        return view('admin.assign-supervisor', compact('supervisors', 'unassignedStudents'));
    }

    /**
     * Store supervisor assignment
     */
    public function storeAssignment(Request $request)
    {
        $request->validate([
            'supervisor_id' => 'required|exists:ssris_users,id',
            'students' => 'required|array',
            'students.*' => 'exists:ssris_users,id',
        ]);

        $students = SsrisUser::whereIn('id', $request->students)->get();
        
        foreach ($students as $student) {
            $student->supervisor_id = $request->supervisor_id;
            $student->save();
        }

        return redirect()->route('admin.assign-supervisor')
            ->with('success', 'Students assigned to supervisor successfully!');
    }

    /**
     * Bulk create students
     */
    public function bulkCreateStudents()
    {
        return view('admin.bulk-create-students');
    }

    /**
     * Store bulk students
     */
    public function storeBulkStudents(Request $request)
    {
        $request->validate([
            'students_data' => 'required|string',
        ]);

        $lines = explode("\n", trim($request->students_data));
        $createdCount = 0;

        foreach ($lines as $line) {
            $data = str_getcsv($line);
            
            if (count($data) >= 4) {
                $name = trim($data[0]);
                $program = trim($data[1]);
                $regNumber = trim($data[2]);
                $year = trim($data[3]);

                // Validate program
                if (!in_array($program, [SsrisUser::PROGRAM_BBICT, SsrisUser::PROGRAM_BHRM, SsrisUser::PROGRAM_BAT])) {
                    continue;
                }

                // Generate username and password
                $username = SsrisUser::generateStudentUsername($program, $regNumber, $year);
                $password = SsrisUser::generateStudentPassword($program, $regNumber, $year);

                // Check if user already exists
                if (!SsrisUser::where('username', $username)->exists()) {
                    SsrisUser::create([
                        'name' => $name,
                        'username' => $username,
                        'role' => SsrisUser::ROLE_STUDENT,
                        'program' => $program,
                        'reg_number' => $regNumber,
                        'year' => $year,
                        'password' => Hash::make($password),
                    ]);

                    $createdCount++;
                }
            }
        }

        return redirect()->route('admin.bulk-create-students')
            ->with('success', "Successfully created {$createdCount} students!");
    }
}
