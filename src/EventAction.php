<?php

namespace MOIREI\EventTracking;

use Illuminate\Support\Arr;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Jobs\ProcessIdentifyEvent;
use MOIREI\EventTracking\Jobs\ProcessTrackEvent;
use MOIREI\EventTracking\Objects\Device;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;
use MOIREI\EventTracking\Objects\User;

class EventAction
{
    protected array $adapters;

    public function __construct(
        protected array $channels,
        protected Device $device,
    ) {
        $this->adapters = EventTracking::$adapters;
    }

    /**
     * Track an event.
     *
     * @param $event
     * @param $properties
     */
    public function track($event, $properties = [])
    {
        $eventPayload = new EventPayload();
        $eventPayload->event = $this->getEventName($event);
        $eventPayload->properties = $this->getEventProperties($eventPayload->event, $properties);
        $eventPayload->device = $this->device;

        if (config('event-tracking.queue.disabled')) {
            EventHandler::event($eventPayload, $this->channels, $this->adapters);
        } else {
            ProcessTrackEvent::dispatch($eventPayload, $this->channels, $this->adapters);
        }
    }

    /**
     * Identify a user.
     *
     * @param  EventUser|string  $user
     * @param  array  $properties
     */
    public function identify(EventUser|string $user, array $properties = [])
    {
        $identity = new IdentityPayload();
        $userObject = new User();

        if (is_string($user)) {
            $userObject->id = $user;
        } else {
            $userObject->id = $user->getId();
            $userObject->name = $user->getName();
            $userObject->firstName = $user->getFirstName();
            $userObject->lastName = $user->getLastName();
            $userObject->email = $user->getEmail();
            $userObject->createdAt = $user->getCreatedDate();
            $properties = array_merge($user->getProperties(), $properties);
        }

        $identity->user = $userObject;
        $identity->ip = $this->device->ip;
        $identity->properties = $properties;

        if (config('event-tracking.queue.disabled')) {
            EventHandler::identify($identity, $this->channels, $this->adapters);
        } else {
            ProcessIdentifyEvent::dispatch($identity, $this->channels, $this->adapters);
        }
    }

    protected function getEventName($event): string
    {
        $event = Helpers::normaliseValue($event);

        return Arr::get(EventTracking::$globalEventMap, "$event.name", $event);
    }

    protected function getEventProperties(string $event, $properties): array
    {
        if ($propertiesKey = Arr::get(EventTracking::$globalEventMap, "$event.properties")) {
            if (is_object($properties)) {
                if (method_exists($properties, $propertiesKey)) {
                    return $properties->$propertiesKey();
                }
                if (property_exists($properties, $propertiesKey)) {
                    return $properties->$propertiesKey;
                }
            }
            if (isset($properties[$propertiesKey])) {
                return $properties[$propertiesKey];
            }
        }

        return (array) $properties;
    }
}
