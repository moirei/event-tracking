<?php

namespace MOIREI\EventTracking\Facades;

use Illuminate\Support\Facades\Facade;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Contracts\EventUserProxy;
use MOIREI\EventTracking\EventAction;
use MOIREI\EventTracking\Objects\User;

/**
 * @method static void track(string $event, array $properties = [])
 * @method static void identify(EventUser|EventUserProxy|User|string $user, array $properties = [])
 * @method static EventAction all()
 * @method static EventAction except($channels)
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
 * @method static void before(mixed $events, Closure $handler)
 * @method static void after(mixed $events, Closure $handler)
 * @method static array getEventHookHandlers(string $hook, $event)
 */
class Events extends Facade
{
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
