<?php

namespace MOIREI\EventTracking\Contracts;

interface TrackableEvent
{
    /**
     * Get tracking name
     *
     * @return string
     */
    public function getEventName();

    /**
     * Get the event properties
     *
     * @return array
     */
    public function getEventProperties();
}
