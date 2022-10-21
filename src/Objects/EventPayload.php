<?php

namespace MOIREI\EventTracking\Objects;

class EventPayload
{
    /**
     * The nromalised original event name before it was mapped.
     *
     * @var string
     */
    public string $originalEventName;

    /**
     * The nromalised event name.
     *
     * @var string
     */
    public string $event;

    /**
     * The event device.
     *
     * @var Device
     */
    public Device $device;

    /**
     * Properties associated witht he event.
     *
     * @var array
     */
    public array $properties;

    /**
     * Active user identified at the time of event.
     *
     * @var User|null
     */
    public ?User $user;
}
