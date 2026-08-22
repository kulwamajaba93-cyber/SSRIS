<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\SsrisUser;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Check if user has the required role
        if ($user->role !== $role) {
            return $this->redirectToDashboard($user);
        }

        return $next($request);
    }

    /**
     * Redirect user to their appropriate dashboard
     */
    private function redirectToDashboard(SsrisUser $user): Response
    {
        return match($user->role) {
            SsrisUser::ROLE_ADMIN => redirect('/admin/dashboard'),
            SsrisUser::ROLE_SUPERVISOR => redirect('/supervisor/dashboard'),
            SsrisUser::ROLE_STUDENT => redirect('/student/dashboard'),
            default => redirect('/login'),
        };
    }
}
