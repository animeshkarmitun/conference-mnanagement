<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     * Usage: middleware('permission:module.action|module.other') => allow if user has ANY
     */
    public function handle(Request $request, Closure $next, string $permissions)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support multiple permissions separated by | (any) or , (any)
        $candidates = preg_split('/[|,]/', $permissions);
        $candidates = array_filter(array_map('trim', $candidates));

        foreach ($candidates as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        // Fallback: deny access
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return redirect()->back()->with('error', 'Access denied. Missing permission: ' . $permissions);
    }
}







