<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;

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
        Passport::$validateKeyPermissions = filter_var(
            env('PASSPORT_VALIDATE_KEY_PERMISSIONS', false),
            FILTER_VALIDATE_BOOL
        );

        Passport::tokensCan([
            'feed:read' => 'Read the community feed',
            'projects:read' => 'Read projects and profiles',
            'messages:read' => 'Read messages and notifications',
        ]);
    }
}
