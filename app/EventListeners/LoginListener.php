<?php

namespace App\EventListeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;


class LoginListener
{
    public function handle(Login $event)
    {
        Log::info("
            ------------
            User #: {$event->user->id} Signed in
            Name: {$event->user->name}
            Email: {$event->user->email}
            ------------
        ");
    }
}