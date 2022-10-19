<?php

namespace MOIREI\EventTracking\Facades;

use Illuminate\Support\Facades\Facade;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\EventAction;

/**
 * @method static void track(string $event, array $properties = [])
 * @method static void identify(EventUser|string $user, array $properties = [])
 * @method static EventAction all()
 * @method static EventAction except($channels)
 * @method static EventAction only($channels)
 * @method static EventAction channel(string $channel)
 * @method static void observe(mixed $models)
 * @method static void listen(mixed $events)
 * @method static void registerAdapter(mixed $adapters)
 * @method static void mapEvent(array $map)
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
