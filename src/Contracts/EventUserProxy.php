<?php

namespace MOIREI\EventTracking\Contracts;

use MOIREI\EventTracking\Objects\User;

interface EventUserProxy
{
    /**
     * Get the event user representation.
     *
     * @return User
     */
    public function getEventUser(): User;
}
