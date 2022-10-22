<?php

namespace MOIREI\EventTracking\Adapters;

use Illuminate\Support\Arr;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

abstract class EventAdapter
{
    /**
     * List of channels to handle.
     *
     * @var string[]
     */
    protected array $channels = [];

    /**
     * List of events to handle.
     *
     * @var string[]
     */
    protected array $only = [];

    /**
     * List of events to omit.
     *
     * @var string[]
     */
    protected array $except = [];

    /**
     * Registered event name map.
     *
     * @var array<string, string|\Closure>
     */
    protected static $eventNameMap = [];

    /**
     * Registered event property map.
     *
     * @var array<string, \Closure>
     */
    protected static $eventPropertyMap = [];

    /**
     * Get a list of channels to apply.
     */
    public function channels()
    {
        return $this->channels;
    }

    /**
     * Get a list of events to handle.
     */
    public function only()
    {
        return $this->only;
    }

    /**
     * Get a list of events to omit.
     */
    public function except()
    {
        return $this->except;
    }

    /**
     * Rename event name.
     *
     * @param  EventPayload  $payload
     * @return string
     */
    public function eventNameAs(EventPayload $payload): string
    {
        return $payload->event;
    }

    /**
     * Rename event properties.
     *
     * @param  EventPayload  $payload
     * @return array
     */
    public function eventPropertiesAs(EventPayload $payload): array
    {
        return $payload->properties;
    }

    /**
     * Get extra event properties.
     *
     * @param  EventPayload  $payload
     * @return array
     */
    public function extraProperties(EventPayload $payload)
    {
        return [
            //
        ];
    }

    /**
     * Map event names.
     *
     * @param  array<string, string|\Closure>  $map
     */
    protected static function mapEvents(array $map)
    {
        foreach ($map as $key => $value) {
            Arr::set(
                static::$eventNameMap,
                static::class.'.'.Helpers::normaliseValue($key),
                $value,
            );
        }
    }

    /**
     * Map an event name.
     *
     * @param  array<string, string|\Closure>  $map
     */
    protected static function mapEvent($key, string $value)
    {
        static::mapEvents([
            Helpers::normaliseValue($key) => $value,
        ]);
    }

    /**
     * Map event properties.
     *
     * @param  array<string, \Closure>  $map
     */
    protected static function mapEventProperties(array $map)
    {
        foreach ($map as $key => $value) {
            Arr::set(
                static::$eventPropertyMap,
                static::class.'.'.Helpers::normaliseValue($key),
                $value
            );
        }
    }

    /**
     * Map event property.
     *
     * @param  string|string[]  $key
     * @param  string|\Closure  $value
     */
    protected static function mapEventProperty($key, string|\Closure $value)
    {
        $keys = is_array($key) ? $key : [$key];
        foreach ($keys as $key) {
            static::mapEventProperties([
                Helpers::normaliseValue($key) => $value,
            ]);
        }
    }

    /**
     * Check if has event property.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function hasMappedEventName($key)
    {
        return Arr::has(
            static::$eventNameMap,
            static::class.'.'.Helpers::normaliseValue($key),
        );
    }

    /**
     * Get event property.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function getMappedEventName($key)
    {
        return Arr::get(
            static::$eventNameMap,
            static::class.'.'.Helpers::normaliseValue($key),
        );
    }

    /**
     * Check if has event property.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function hasMappedEventProperty($key)
    {
        return Arr::has(
            static::$eventPropertyMap,
            static::class.'.'.Helpers::normaliseValue($key),
        );
    }

    /**
     * Get event property.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function getMappedEventProperty($key)
    {
        return Arr::get(
            static::$eventPropertyMap,
            static::class.'.'.Helpers::normaliseValue($key),
        );
    }

    /**
     * Configure adapter
     */
    public function configure()
    {
        //
    }
}
