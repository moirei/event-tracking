<?php

namespace MOIREI\EventTracking;

use Closure;
use MOIREI\EventTracking\Channels\EventChannel;
use MOIREI\EventTracking\Facades\Events;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;

final class EventHandler
{
    /**
     * Send the event data to the given channels.
     *
     * @param  EventPayload  $data
     * @param  string[]  $channels
     * @param  string[]  $adapters
     */
    public static function event(EventPayload $data, array $channels, array $adapters)
    {
        static::eachChannel($channels, function (EventChannel $channel, string $channelKey) use ($adapters, $data) {
            $data = Helpers::applyAdapterTransform($adapters, $channelKey, $data);
            $channel->track($data);
        });
        EventHandler::runHooks('after', $data->originalEventName, [$data, $channels]);
    }

    /**
     * Send the identify event data to the given channels.
     *
     * @param  IdentityPayload  $data
     * @param  string[]  $channels
     * @param  string[]  $adapters
     */
    public static function identify(IdentityPayload $data, array $channels, array $adapters)
    {
        static::eachChannel($channels, function (EventChannel $channel) use ($data) {
            $channel->identify($data);
        });
    }

    /**
     * Run hooks for events.
     *
     * @param  string  $hook
     * @param  mixed  $event
     * @param  array  $args
     * @return bool
     */
    public static function runHooks(string $hook, $event, array $args): bool
    {
        $handlers = Events::getEventHookHandlers($hook, $event);
        $results = [];
        foreach ($handlers as $handler) {
            $results[] = call_user_func_array($handler, $args);
        }

        return ! Helpers::isAny($results, false);
    }

    protected static function eachChannel(array $channels, Closure $callback)
    {
        foreach ($channels as $channel) {
            $instance = Events::getChannel($channel);
            $callback($instance, $channel);
        }
    }
}
