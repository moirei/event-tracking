<?php

namespace MOIREI\EventTracking\Testing;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\ReflectsClosures;
use MOIREI\EventTracking\EventAction;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\Device;
use PHPUnit\Framework\Assert as PHPUnit;

class EventTrackingFake extends EventTracking
{
    use ReflectsClosures;

    public array $events = [];
    public array $eventsToFake;
    protected EventTracking $eventTracking;
    protected Device $device;

    /**
     * Create a new event fake instance.
     *
     * @param  \MOIREI\EventTracking\EventTracking  $eventTracking
     * @param  array|string  $eventsToFake
     * @return void
     */
    public function __construct(
        EventTracking $eventTracking,
        $eventsToFake = [],
        public bool $shouldFakeIdentify
    ) {
        $this->eventTracking = $eventTracking;
        $this->eventsToFake = Arr::wrap($eventsToFake);
        $this->device = Helpers::getDeviceData(request());
    }

    /**
     * Assert if an event was tracked based on a truth-test callback.
     *
     * @param  string|\Closure  $event
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertTracked($event, $callback = null)
    {
        if ($event instanceof Closure) {
            [$event, $callback] = [$this->firstClosureParameterType($event), $event];
        }

        if (is_int($callback)) {
            return $this->assertTrackedTimes($event, $callback);
        }

        PHPUnit::assertTrue(
            $this->tracked($event, $callback)->count() > 0,
            "The expected [{$event}] event was not tracked."
        );
    }

    /**
     * Assert if an event was tracked based on a truth-test callback.
     *
     * @param  string|\Closure  $event
     * @param  string[]|string  $channels
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertTrackedOnChannels($event, $channels, $callback = null)
    {
        if ($event instanceof Closure) {
            [$event, $callback] = [$this->firstClosureParameterType($event), $event];
        }

        if (is_int($callback)) {
            return $this->assertTrackedTimesOnChannels($event, $channels, $callback);
        }

        $channelNames = implode(',', Arr::wrap($channels));

        PHPUnit::assertTrue(
            $this->trackedOnChannels($event, $channels, $callback)->count() > 0,
            "The expected [{$event}] event was not tracked on channels [{$channelNames}]."
        );
    }

    /**
     * Assert if an event was tracked a number of times.
     *
     * @param  string  $event
     * @param  int  $times
     * @return void
     */
    public function assertTrackedTimes($event, $times = 1)
    {
        $count = $this->tracked($event)->count();

        PHPUnit::assertSame(
            $times,
            $count,
            "The expected [{$event}] event was tracked {$count} times instead of {$times} times."
        );
    }

    /**
     * Assert if an event was tracked a number of times.
     *
     * @param  string  $event
     * @param  string[]|string  $channels
     * @param  int  $times
     * @return void
     */
    public function assertTrackedTimesOnChannels($event, $channels, $times = 1)
    {
        $count = $this->trackedOnChannels($event, $channels)->count();
        $channelNames = implode(',', Arr::wrap($channels));

        PHPUnit::assertSame(
            $times,
            $count,
            "The expected [{$event}] event was tracked {$count} times instead of {$times} times on channels [{$channelNames}]."
        );
    }

    /**
     * Determine if an event was tracked based on a truth-test callback.
     *
     * @param  string|\Closure  $event
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotTracked($event, $callback = null)
    {
        if ($event instanceof Closure) {
            [$event, $callback] = [$this->firstClosureParameterType($event), $event];
        }

        PHPUnit::assertCount(
            0,
            $this->tracked($event, $callback),
            "The unexpected [{$event}] event was tracked."
        );
    }

    /**
     * Determine if an event was tracked based on a truth-test callback.
     *
     * @param  string|\Closure  $event
     * @param  string[]|string  $channels
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotTrackedOnChannels($event, $channels, $callback = null)
    {
        if ($event instanceof Closure) {
            [$event, $callback] = [$this->firstClosureParameterType($event), $event];
        }

        PHPUnit::assertCount(
            0,
            $this->trackedOnChannels($event, $channels, $callback),
            "The unexpected [{$event}] event was tracked."
        );
    }

    /**
     * Assert that no events were tracked.
     *
     * @return void
     */
    public function assertNothingTracked()
    {
        $count = count(Arr::flatten($this->events));

        PHPUnit::assertSame(
            0,
            $count,
            "{$count} unexpected events were tracked."
        );
    }

    /**
     * Get all of the events matching a truth-test callback.
     *
     * @param  string  $event
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function tracked($event, $callback = null)
    {
        if (!$this->hasTracked($event)) {
            return collect();
        }

        $callback = $callback ?: fn () => true;

        return collect($this->events[$event])->filter(
            fn ($data) => $callback(...Arr::get($data, 'args', []))
        );
    }

    /**
     * Get all of the events matching a truth-test callback a given channels.
     *
     * @param  string  $event
     * @param  string[]|string  $channels
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function trackedOnChannels($event, $channels, $callback = null)
    {
        if (!$this->hasTracked($event)) {
            return collect();
        }

        $callback = $callback ?: fn () => true;
        $channels = Arr::wrap($channels);

        return collect($this->events[$event])->filter(
            function ($data) use ($callback, $channels) {
                $trackedChannels = Arr::get($data, 'channels', []);
                return Helpers::isAllIn($channels, $trackedChannels) && $callback(...Arr::get($data, 'args', []));
            }
        );
    }

    /**
     * Determine if the given event has been tracked.
     *
     * @param  string  $event
     * @return bool
     */
    public function hasTracked($event)
    {
        return isset($this->events[$event]) && !empty($this->events[$event]);
    }

    /**
     * Create a new event action.
     *
     * @param  array  $channels
     * @return EventAction
     */
    public function makeEvent(array $channels): EventAction
    {
        return new EventActionFake($this, $channels, $this->device);
    }
}
