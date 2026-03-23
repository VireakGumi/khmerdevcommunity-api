<?php

namespace App\Observers;

use App\Models\CommunityNotification;
use App\Services\PushNotificationService;

class CommunityNotificationObserver
{
    public function created(CommunityNotification $notification): void
    {
        app(PushNotificationService::class)->sendForNotification($notification);
    }
}
