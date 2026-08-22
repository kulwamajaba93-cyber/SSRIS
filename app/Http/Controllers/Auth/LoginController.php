<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(Request $request)
    {
        // Ensure session is started and regenerate token to prevent CSRF issues
        if (!$request->hasSession()) {
            $request->session()->start();
        }
        $request->session()->regenerate();

        return view('auth.login');
    }

    /**
     * Handle a login request
     */
    public function login(Request $request)
    {
        // Validate the login request
        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Determine if this is a student login (username) or email login
        $loginField = $this->getLoginField($request->input('username'));
        $credentials = [
            $loginField => $request->input('username'),
            'password' => $request->input('password'),
        ];

        // Attempt to authenticate
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect to dashboard without interfering cache headers
            return redirect($user->getDashboardRoute());
        }

        // If login fails
        return back()
            ->with('error', 'Invalid credentials. Please check your login information and try again.')
            ->withInput($request->except('password'));
    }

    /**
     * Validate the login request
     */
    protected function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'The username or email field is required.',
            'password.required' => 'The password field is required.',
        ]);
    }

    /**
     * Determine the login field based on input format
     */
    protected function getLoginField($login): string
    {
        // If the login contains '/', it's a student username format
        if (strpos($login, '/') !== false) {
            return 'username';
        }
        
        // If it contains @, it's an email
        if (strpos($login, '@') !== false) {
            return 'email';
        }
        
        // Default to username for students
        return 'username';
    }

    /**
     * Log the user out
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
