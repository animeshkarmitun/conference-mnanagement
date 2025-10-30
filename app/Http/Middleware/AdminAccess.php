<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        
        // Check if user has admin or superadmin role
        if (!$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            // TEMPORARY FIX: Allow access for testing, but show warning
            // TODO: Remove this after fixing the role assignment issue
            if ($user->email === 'conferencescgs@gmail.com') {
                // Allow superadmin email to pass through
                return $next($request);
            }
            
            // If user has no roles at all, redirect to role-based dashboard
            if (!$user->hasAnyRole()) {
                return redirect()->route('role-dashboard')->with('error', 'Access denied. No role assigned. Please contact an administrator.');
            }
            
            // Redirect to user's default dashboard route
            $dashboardRoute = $user->getDefaultDashboardRoute();
            return redirect()->route($dashboardRoute)->with('error', 'Access denied. This feature is only available to administrators.');
        }
        return $next($request);
    }
}
