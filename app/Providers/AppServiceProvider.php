<?php

namespace App\Providers;

use App\Models\CommunityNotification;
use App\Observers\CommunityNotificationObserver;
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
        Passport::$validateKeyPermissions = false;
        CommunityNotification::observe(CommunityNotificationObserver::class);

        Passport::tokensCan([
            'feed:read' => 'Read the community feed',
            'projects:read' => 'Read projects and profiles',
            'messages:read' => 'Read messages and notifications',
        ]);
    }
}
