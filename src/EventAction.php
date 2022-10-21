<?php

namespace MOIREI\EventTracking;

use Illuminate\Support\Arr;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Contracts\EventUserProxy;
use MOIREI\EventTracking\Facades\Events;
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
        $this->adapters = EventTracking::getAdapters();
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
        $eventPayload->originalEventName = Helpers::normaliseValue($event);
        $eventPayload->event = $this->getEventMappedName($eventPayload->originalEventName);
        $eventPayload->properties = $this->getEventProperties($eventPayload->event, $properties);
        $eventPayload->device = $this->device;
        $eventPayload->user = Events::user();

        $shouldRun = EventHandler::runHooks('before', $eventPayload->originalEventName, [$eventPayload, $properties, $this->channels]);

        if ($shouldRun) {
            if ($eventPayload->user !== ($user = Events::user())) {
                $eventPayload->user = $user;
            }
            if (config('event-tracking.queue.disabled')) {
                EventHandler::event($eventPayload, $this->channels, $this->adapters);
            } else {
                ProcessTrackEvent::dispatch($eventPayload, $this->channels, $this->adapters);
            }
        }
    }

    /**
     * Identify a user.
     *
     * @param  EventUser|EventUserProxy|User|string  $user
     * @param  array  $properties
     */
    public function identify(EventUser|EventUserProxy|User|string $user, array $properties = [])
    {
        $identity = new IdentityPayload();
        $userObject = $user instanceof User ? $user : new User();

        if (is_string($user)) {
            $userObject->id = $user;
        } elseif ($user instanceof EventUserProxy) {
            $userObject = $user->getEventUser();
        } elseif ($user instanceof EventUser) {
            $userObject->id = $user->getId();
            $userObject->name = $user->getName();
            $userObject->firstName = $user->getFirstName();
            $userObject->lastName = $user->getLastName();
            $userObject->email = $user->getEmail();
            $userObject->createdAt = $user->getCreatedDate();
            $userObject->fill($user->getProperties());
        }

        $identity->user = $userObject;
        $identity->ip = $this->device->ip;
        $identity->properties = array_merge($userObject->getArrayCopy(), $properties);

        if (! Events::user()) {
            Events::user($userObject);
        }

        if (config('event-tracking.queue.disabled')) {
            EventHandler::identify($identity, $this->channels, $this->adapters);
        } else {
            ProcessIdentifyEvent::dispatch($identity, $this->channels, $this->adapters);
        }
    }

    protected function getEventMappedName(string $event): string
    {
        return Arr::get(Events::getEventMaps(), "$event.name", $event);
    }

    protected function getEventProperties(string $event, $properties): array
    {
        if ($propertiesKey = Arr::get(Events::getEventMaps(), "$event.properties")) {
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

        return array_merge(
            (array) $properties,
            Events::getSuperProperties()
        );
    }
}
