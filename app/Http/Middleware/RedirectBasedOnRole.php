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
                // Redirect to user's default dashboard route
                $dashboardRoute = $user->getDefaultDashboardRoute();
                return redirect()->route($dashboardRoute);
            }
        }

        return $next($request);
    }
}
