<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.cours');
                case 'agent':
                    return redirect()->route('agent.cours');
                case 'client':
                    return redirect()->route('cours');
                default:
                    return redirect()->route('home');
            }
        }
        return $next($request);
    }
}
