<?php

namespace GeneaLabs\LaravelMixpanel\Listeners;

use MOIREI\EventTracking\Facades\Events;

class Login
{
    /**
     * @param  Illuminate\Auth\Events\Login  $event
     */
    public function handle($event)
    {
        Events::identify($event->user->getKey());
    }
}
