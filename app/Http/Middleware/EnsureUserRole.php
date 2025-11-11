<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        $allowedRoles = strpos($roles, '|') !== false ? explode('|', $roles) : [$roles];
        // Check if the user's role is in the allowed roles
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
