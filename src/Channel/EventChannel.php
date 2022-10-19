<?php

namespace MOIREI\EventTracking\Channel;

use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;

abstract class EventChannel
{
    /**
     * Initialise channel.
     *
     * @param  array  $config
     */
    public function initialize(array $config)
    {
        //
    }

    /**
     * Send tack event data to channel.
     *
     * @param  EventPayload  $data
     */
    abstract public function track(EventPayload $data);

    /**
     * Send identity data to channel.
     *
     * @param  IdentityPayload  $data
     */
    abstract public function identify(IdentityPayload $data);
}
