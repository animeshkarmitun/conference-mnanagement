<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictParticipantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentPath = $request->path();
        
        // Define public routes that should be accessible to everyone (including non-authenticated users)
        $publicRoutes = [
            '', // Root route
            'login',
            'register',
            'password',
            'debug', // Debug routes
            'api', // API routes
            'passwordless-login', // Allow passwordless login verification
        ];
        
        // Check if current path is a public route
        $isPublicRoute = false;
        foreach ($publicRoutes as $publicRoute) {
            // Exact match or starts with the route
            if ($currentPath === $publicRoute || str_starts_with($currentPath, $publicRoute . '/')) {
                $isPublicRoute = true;
                break;
            }
        }
        
        // If it's a public route, allow access
        if ($isPublicRoute) {
            return $next($request);
        }
        
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user is a participant (has participant records)
        $isParticipant = $user->participants()->exists();
        
        // Check if user has admin/superadmin roles
        $hasAdminRole = $user->roles()->whereIn('name', ['admin', 'superadmin'])->exists();
        
        // If user is a participant but not admin, restrict access
        if ($isParticipant && !$hasAdminRole) {
            // Define allowed routes for participants
            $allowedRoutes = [
                'my-profile',
                'notifications',
                'logout', // Allow logout
            ];
            
            // Check if current path matches any allowed route
            $isAllowed = false;
            foreach ($allowedRoutes as $allowedRoute) {
                // Exact match or starts with the route
                if ($currentPath === $allowedRoute || str_starts_with($currentPath, $allowedRoute . '/')) {
                    $isAllowed = true;
                    break;
                }
            }
            
            // If not allowed, return 404
            if (!$isAllowed) {
                abort(404, 'Page not found');
            }
        }

        return $next($request);
    }
}
