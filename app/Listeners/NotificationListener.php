<?php

namespace App\Listeners;

use App\Events\SendNotification;
use App\Services\MockyService;

class NotificationsListeners
{
    public function handle(SendNotification $event)
    {
        return app(MockyService::class)->notifyUser($event->transaction->wallet->user->id);
    }
}
