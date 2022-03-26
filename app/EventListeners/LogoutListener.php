<?php

namespace App\EventListeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogoutListener
{
    public function handle(Logout $event)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        // Log::info("User # {$event->user->id} Signed out");
        Log::info("
            ------------
            User #: {$event->user->id} Signed out
            Name: {$event->user->name}
            Email: {$event->user->email}
            ip: {$ip}
            ------------
        ");
    }
}