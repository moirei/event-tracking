<?php

namespace MOIREI\EventTracking\Testing;

use MOIREI\EventTracking\EventAction;
use MOIREI\EventTracking\Objects\Device;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;

class EventActionFake extends EventAction
{
    /**
     * Create a new event fake instance.
     *
     * @param  \MOIREI\EventTracking\EventTracking  $eventTracking
     * @param  array  $channels
     * @param  Device  $device
     * @return void
     */
    public function __construct(
        protected EventTrackingFake $eventTracking,
        protected array $channels,
        protected Device $device,
    ) {
        parent::__construct($channels, $device);
    }

    protected function handleEvent(EventPayload $eventPayload, $event)
    {
        if ($this->shouldFakeEvent($event, $eventPayload)) {
            $this->eventTracking->events[$event][] = [
                'args' => [$event, $eventPayload],
                'channels' => $this->channels,
                // 'adapters' => $this->adapters,
            ];
        } else {
            return parent::handleEvent($eventPayload, $event);
        }
    }

    protected function handleIdentify(IdentityPayload $identity)
    {
        if ($this->eventTracking->shouldFakeIdentify) {
            //
        } else {
            return parent::handleIdentify($identity);
        }
    }

    protected function shouldFakeEvent($eventName, $eventPayload)
    {
        if (empty($this->eventTracking->eventsToFake)) {
            return true;
        }

        return collect($this->eventTracking->eventsToFake)
            ->filter(function ($event) use ($eventName, $eventPayload) {
                return $event instanceof \Closure
                    ? $event($eventName, $eventPayload)
                    : $event === $eventName;
            })
            ->isNotEmpty();
    }
}
