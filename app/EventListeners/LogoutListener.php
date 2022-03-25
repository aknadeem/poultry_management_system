<?php

namespace App\EventListeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogoutListener
{
    public function handle(Logout $event)
    {
        Log::info("User # {$event->user->id} Signed out");
    }
}