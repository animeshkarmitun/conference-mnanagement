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
            // If user has relevant permission for the current module, allow access
            $routeName = $request->route()?->getName();
            $segments = array_values(array_filter(explode('/', $currentPath)));

            $permissionsConfig = config('permissions', []);
            $moduleKeys = array_keys($permissionsConfig);

            $hasModulePermission = function (string $module) use ($user, $permissionsConfig): bool {
                if (!isset($permissionsConfig[$module])) {
                    return false;
                }
                if ($user->hasPermission($module . '.view') || $user->hasPermission($module . '.*')) {
                    return true;
                }
                foreach ($permissionsConfig[$module] as $action) {
                    if ($user->hasPermission($module . '.' . $action)) {
                        return true;
                    }
                }
                return false;
            };

            // Admin-prefixed sections: map the second segment to a module key
            if (!empty($segments) && $segments[0] === 'admin') {
                $adminSecond = $segments[1] ?? '';
                // Aliases for admin sections to module keys
                $adminAliases = [
                    'settings' => 'email-settings',
                    'email-tracking' => 'email-tracking',
                    'backup' => 'backup',
                    // Travel feature group under a single module key
                    'itineraries' => 'travel',
                    'travel-conflicts' => 'travel',
                    'room-allocations' => 'travel',
                    'export-itinerary' => 'travel',
                ];
                $module = $adminAliases[$adminSecond] ?? $adminSecond;
                if (in_array($module, $moduleKeys, true) && $hasModulePermission($module)) {
                    return $next($request);
                }
            }

            // Direct module path checks using config keys
            $first = $segments[0] ?? '';
            if (in_array($first, $moduleKeys, true) && $hasModulePermission($first)) {
                return $next($request);
            }

            // Route name-based checks (fallback)
            if ($routeName) {
                $parts = explode('.', $routeName);
                // Try each progressive prefix as a module key
                for ($i = 1; $i <= count($parts); $i++) {
                    $candidate = implode('.', array_slice($parts, 0, $i));
                    if (in_array($candidate, $moduleKeys, true) && $hasModulePermission($candidate)) {
                        return $next($request);
                    }
                }
                // Simple base check
                $base = $parts[0] ?? '';
                if ($base && in_array($base, $moduleKeys, true) && $hasModulePermission($base)) {
                    return $next($request);
                }
            }

            // Define allowed routes for participants
            $allowedRoutes = [
                'my-profile',
                'participants/profile', // legacy alias route name path
                'notifications',
                'participant-dashboard',
                'role-dashboard',
                'dashboard-tasker',
                'event-coordinator',
                'how-to-use', // How to Use guide page
                'guide', // legacy path alias for guide
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
            
            // If not allowed, redirect to the user's default dashboard route instead of 404
            if (!$isAllowed) {
                $dashboardRoute = auth()->user()->getDefaultDashboardRoute();
                return redirect()->route($dashboardRoute);
            }
        }

        return $next($request);
    }
}
