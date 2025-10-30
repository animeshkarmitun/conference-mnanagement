<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Blade conditional directives for permissions
        Blade::if('permission', function (string $permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        Blade::if('anypermission', function (string $permissions) {
            if (!auth()->check()) {
                return false;
            }
            $list = preg_split('/[|,]/', $permissions);
            $list = array_filter(array_map('trim', $list));
            foreach ($list as $perm) {
                if (auth()->user()->hasPermission($perm)) {
                    return true;
                }
            }
            return false;
        });
    }
}
