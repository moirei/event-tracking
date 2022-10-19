<?php

namespace MOIREI\EventTracking\Objects;

class IdentityPayload
{
    public User $user;

    public array $properties;

    public ?string $ip;
}
