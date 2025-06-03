<?php

namespace MOIREI\EventTracking\Listeners;

use MOIREI\EventTracking\Facades\Events;

class Login
{
    /**
     * @param  Illuminate\Auth\Events\Login  $event
     */
    public function handle($event)
    {
        if(Events::isAutoTrackingEnabled()){
            Events::identify($event->user->getKey());
        }
    }
}
