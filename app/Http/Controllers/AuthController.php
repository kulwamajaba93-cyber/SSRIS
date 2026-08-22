<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\SsrisUser;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Check if login is email or username
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Find user by email or username
        $user = SsrisUser::where($loginField, $login)->first();

        if (!$user) {
            return back()->withErrors([
                'login' => 'Invalid credentials.',
            ])->withInput($request->only('login'));
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            return back()->withErrors([
                'login' => 'Invalid credentials.',
            ])->withInput($request->only('login'));
        }

        // For students, validate the username format matches password pattern
        if ($user->isStudent()) {
            $expectedPassword = SsrisUser::generateStudentPassword(
                $user->program,
                $user->reg_number,
                $user->year
            );
            
            // Additional validation for student password format
            if ($password !== $expectedPassword && !Hash::check($password, $user->password)) {
                return back()->withErrors([
                    'login' => 'Invalid student credentials.',
                ])->withInput($request->only('login'));
            }
        }

        // Log the user in
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Redirect based on role
        return $this->redirectBasedOnRole($user);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(SsrisUser $user)
    {
        return match($user->role) {
            SsrisUser::ROLE_ADMIN => redirect('/admin/dashboard'),
            SsrisUser::ROLE_SUPERVISOR => redirect('/supervisor/dashboard'),
            SsrisUser::ROLE_STUDENT => redirect('/student/dashboard'),
            default => redirect('/login'),
        };
    }
}
