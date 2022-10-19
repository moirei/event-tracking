<?php

namespace MOIREI\EventTracking\Objects;

class EventPayload
{
    public string $event;

    public Device $device;

    public array $properties;
}
