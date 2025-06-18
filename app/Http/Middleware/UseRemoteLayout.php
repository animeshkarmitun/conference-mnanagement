<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class UseRemoteLayout
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('production')) {
            View::share('layout', 'layouts.remote');
        } else {
            View::share('layout', 'layouts.app');
        }

        return $next($request);
    }
} 