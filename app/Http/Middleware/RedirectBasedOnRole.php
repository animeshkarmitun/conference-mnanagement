<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
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
        if (Auth::check()) {
            $user = Auth::user();
            
            // If user is trying to access the main dashboard but doesn't have admin privileges
            if ($request->is('dashboard') && !$user->hasRole('admin') && !$user->hasRole('superadmin')) {
                // Redirect based on role
                if ($user->hasRole('attendee') || $user->hasRole('speaker')) {
                    return redirect()->route('participant-dashboard');
                }
                
                if ($user->hasRole('tasker')) {
                    return redirect()->route('dashboard.tasker');
                }
                
                if ($user->hasRole('event_coordinator')) {
                    return redirect()->route('event-coordinator.dashboard');
                }
                
                // Default fallback for participants
                return redirect()->route('participant-dashboard');
            }
        }

        return $next($request);
    }
}
