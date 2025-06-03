<?php

namespace MOIREI\EventTracking\Listeners;

use MOIREI\EventTracking\Contracts\TrackableEvent;
use MOIREI\EventTracking\Facades\Events;

class TrackableEventListener
{
    /**
     * @param  TrackableEvent  $event
     */
    public function handle($event)
    {
        if(Events::isAutoTrackingEnabled()){
            if ($event instanceof TrackableEvent) {
                Events::track($event->getEventName(), $event->getEventProperties());
            } else {
                Events::track(get_class($event), get_object_vars($event));
            }
        }
    }
}
