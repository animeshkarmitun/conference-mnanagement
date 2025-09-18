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
            return redirect()->back()->with('error', 'Access denied. This feature is only available to administrators.');
        }

        return $next($request);
    }
}
