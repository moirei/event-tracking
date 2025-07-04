<?php

namespace MOIREI\EventTracking\Facades;

use Closure;
use Illuminate\Support\Facades\Facade;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Contracts\EventUserProxy;
use MOIREI\EventTracking\EventAction;
use MOIREI\EventTracking\Objects\User;
use MOIREI\EventTracking\Testing\EventTrackingFake;

/**
 * @method static void track($event, array $properties = [])
 * @method static void identify(EventUser|EventUserProxy|User|string $user, array $properties = [])
 * @method static EventAction all()
 * @method static EventAction except($channels)
 * @method static EventAction on($channels)
 * @method static EventAction only($channels)
 * @method static EventAction channel(string $channel)
 * @method static void observe(mixed $models)
 * @method static void listen(mixed $events)
 * @method static void registerAdapter(mixed $adapters)
 * @method static void registerChannel(array $channels)
 * @method static void mapEvent(array $map)
 * @method static array getEventMaps()
 * @method static void superProperties(array $map)
 * @method static array getSuperProperties()
 * @method static User|EventUser|EventUserProxy|null user(User|EventUser|EventUserProxy|null $user = null)
 * @method static \MOIREI\EventTracking\Channels\EventChannel getChannel(string $channel)
 * @method static void before(mixed $events, \Closure $handler)
 * @method static void after(mixed $events, \Closure $handler)
 * @method static array getEventHookHandlers(string $hook, $event)
 * @method static void assertTracked($event, $callback = null)
 * @method static void assertTrackedOnChannels($event, $channels, $callback = null)
 * @method static void assertTrackedTimes($event, $times = 1)
 * @method static void assertTrackedTimesOnChannels($event, $channels, $times = 1)
 * @method static void assertNotTracked($event, $callback = null)
 * @method static void assertNotTrackedOnChannels($event, $channels, $callback = null)
 * @method static void assertNothingTracked()
 * @method static \Illuminate\Support\Collection tracked($event, $callback = null)
 * @method static boolean hasTracked($event)
 */
class Events extends Facade
{
    protected static bool $autoTrackingEnabled = true;

    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $eventsToFake
     * @param  bool  $identify
     * @return \Illuminate\Support\Testing\Fakes\EventFake
     */
    public static function fake($eventsToFake = [], $shouldFakeIdentify = true)
    {
        static::swap($fake = new EventTrackingFake(static::getFacadeRoot(), $eventsToFake, $shouldFakeIdentify));
        return $fake;
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
     *
     * @param  callable  $callable
     * @param  array  $eventsToFake
     * @return mixed
     */
    public static function fakeFor(callable $callable, array $eventsToFake = [])
    {
        $originalEventTracking = static::getFacadeRoot();

        static::fake($eventsToFake);

        return tap($callable(), function () use ($originalEventTracking) {
            static::swap($originalEventTracking);
        });
    }

    /**
     * Replace the events with a fake that fakes all events except the given events.
     *
     * @param  string[]|string  $eventsToAllow
     * @param  bool  $shouldFakeIdentify
     * @return \Illuminate\Support\Testing\Fakes\EventFake
     */
    public static function fakeExcept($eventsToAllow, $shouldFakeIdentify = true)
    {
        return static::fake([
            function ($eventName) use ($eventsToAllow) {
                return !in_array($eventName, (array) $eventsToAllow);
            },
        ], $shouldFakeIdentify);
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
     *
     * @param  callable  $callable
     * @param  array  $eventsToAllow
     * @return mixed
     */
    public static function fakeExceptFor(callable $callable, array $eventsToAllow = [])
    {
        $originalEventTracking = static::getFacadeRoot();

        static::fakeExcept($eventsToAllow);

        return tap($callable(), function () use ($originalEventTracking) {
            static::swap($originalEventTracking);
        });
    }

    /**
     * Enable automatic event tracking globally.
     *
     * When enabled, events dispatched via listen/observe will be tracked automatically.
     *
     * @return void
     */
    public static function enableAutoTracking(): void
    {
        static::$autoTrackingEnabled = true;
    }

    /**
     * Disable automatic event tracking globally.
     *
     * Useful when running background tasks, commands, or testing
     * where you want to prevent auto-dispatching events.
     *
     * @return void
     */
    public static function disableAutoTracking(): void
    {
        static::$autoTrackingEnabled = false;
    }

    /**
     * Check if automatic event tracking is currently enabled.
     *
     * @return bool
     */
    public static function isAutoTrackingEnabled(): bool
    {
        return static::$autoTrackingEnabled;
    }

    /**
     * Temporarily disable automatic event tracking while executing the given callback.
     *
     * Automatically restores the previous auto-tracking state after the callback runs.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public static function withoutAutoTracking(Closure $callback): mixed
    {
        $original = static::$autoTrackingEnabled;

        static::$autoTrackingEnabled = false;

        try {
            return $callback();
        } finally {
            static::$autoTrackingEnabled = $original;
        }
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'eventTracking';
    }
}
