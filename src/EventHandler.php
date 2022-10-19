<?php

namespace MOIREI\EventTracking;

use Closure;
use MOIREI\EventTracking\Channel\EventChannel;
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

    protected static function eachChannel(array $channels, Closure $callback)
    {
        foreach ($channels as $channel) {
            $config = config('event-tracking.channels.'.$channel);
            $options = $config['config'];
            if (is_string($options)) {
                $options = config($options, []);
            }
            /** @var EventChannel */
            $instance = app()->make($config['handler']);
            $instance->initialize($options);
            $callback($instance, $channel);
        }
    }
}