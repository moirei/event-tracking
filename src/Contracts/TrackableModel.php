<?php

namespace MOIREI\EventTracking\Contracts;

interface TrackableModel
{
    /**
     * Get tracking name
     *
     * @return string
     */
    public function getEventName(string $event);

    /**
     * Get the event properties
     *
     * @return array
     */
    public function getEventProperties(string $event);
}
