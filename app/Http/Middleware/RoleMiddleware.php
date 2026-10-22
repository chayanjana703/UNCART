<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please sign in to access this area.');
        }

        $user = Auth::user();

        if ($user->is_blocked) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended by administration.');
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            // If user is admin trying to access vendor, let them; or redirect to appropriate dashboard
            if ($user->isAdmin()) {
                return $next($request);
            }

            return redirect()->route('home')->with('error', 'You do not have permission to view that page.');
        }

        return $next($request);
    }
}
