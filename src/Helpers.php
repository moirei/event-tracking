<?php

namespace MOIREI\EventTracking;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MOIREI\EventTracking\Adapters\EventAdapter;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\Device as DeviceObject;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Device;
use Sinergi\BrowserDetector\Os;

class Helpers
{
    public static array $adaptersCache = [];

    /**
     * Get any of the keys from object or array.
     * Allows "dot" notation.
     *
     * @param  mixed  $target
     * @param  string[]  $keys
     * @param  mixed  $default
     */
    public static function getAny($target, array $keys, mixed $default = null): mixed
    {
        $data = $default;
        foreach ($keys as $key) {
            if (($x = data_get($target, $key)) !== null) {
                $data = $x;
                break;
            }
        }

        return $data;
    }

    /**
     * If any of the items in array are of value
     *
     * @param  array  $array
     * @param  mixed  $value
     * @return bool
     */
    public static function isAny(array $array, mixed $value): bool
    {
        if (!count($array)) {
            return false;
        }
        $array = array_map(fn ($item) => $item instanceof \UnitEnum ? $item->value : $item, $array);
        $value = $value instanceof \UnitEnum ? $value->value : $value;

        $inArray = false;

        foreach ($array as $x) {
            if (($inArray = ($x === $value))) {
                break;
            }
        }

        return $inArray;
    }

    /**
     * If all of the items in arr1 are arr2
     *
     * @param  array  $array
     * @param  mixed  $value
     * @return bool
     */
    public static function isAllIn(array $array1, array $array2): bool
    {
        $inArray = true;

        foreach ($array1 as $v) {
            if (!($inArray = in_array($v, $array2))) {
                break;
            }
        }

        return $inArray;
    }

    /**
     * Get a key from array or object.
     *
     * @param  mixed  $data
     * @param  string  $key
     * @param  mixed  $default
     */
    public static function get($array, string $key, $default = null): mixed
    {
        return data_get($array, $key, $default);
    }

    /**
     * Check if value is an enum.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isEnum($value): bool
    {
        return $value instanceof \UnitEnum;
    }

    /**
     * Normalize enum or premitive value.
     *
     * @param  mixed  $value
     */
    public static function normaliseValue($value)
    {
        if (is_array($value)) {
            return array_map(function ($v) {
                return static::normaliseValue($v);
            }, $value);
        }

        return (string) (static::isEnum($value) ? $value->value : $value);
    }

    /**
     * Resolve adapter instances from class strings.
     *
     * @param  string[]  $adapters
     * @return \MOIREI\EventTracking\Adapters\EventAdapter[]
     */
    public static function resolveAdapters(array $adapters): array
    {
        // TODO: throw conflict/resolution error when multile adapters register same mappers
        return array_map(function ($adapter) {
            if (!isset(static::$adaptersCache[$adapter])) {
                /** @var \MOIREI\EventTracking\Adapters\EventAdapter */
                $instance = app($adapter);
                $instance->configure();
                static::$adaptersCache[$adapter] = $instance;
            }

            return static::$adaptersCache[$adapter];
        }, $adapters);
    }

    /**
     * Resolve adapter instances from class strings.
     *
     * @param  string[]  $adapters
     * @param  string  $channelKey
     * @param  EventPayload  $data
     * @return EventAdapter|null
     */
    public static function getAdapter(array $adapters, string $channelKey, EventPayload $data): EventAdapter|null
    {
        /** @var EventAdapter[] */
        $adapters = static::resolveAdapters($adapters);

        /** @var EventAdapter */
        return Arr::first($adapters, function (EventAdapter $adapter) use ($channelKey, $data) {
            $channels = $adapter->channels();
            if (!in_array('*', $channels) && count($channels) && !in_array($channelKey, $channels)) {
                return false;
            }

            $only = static::normaliseValue($adapter->only());
            if (count($only)) {
                return in_array($data->event, $only);
            }
            $except = static::normaliseValue($adapter->except());

            return !in_array($data->event, $except);
        });
    }

    /**
     * Resolve adapter instances from class strings.
     *
     * @param  string[]  $adapters
     * @param  string  $channelKey
     * @param  EventPayload  $data
     * @return EventPayload
     */
    public static function applyAdapterTransform(array $adapters, string $channelKey, EventPayload $data)
    {
        $adapter = static::getAdapter($adapters, $channelKey, $data);

        if ($adapter) {
            $event = $adapter->eventNameAs($data);
            if ($adapter->hasMappedEventName($event)) {
                $event = $adapter->getMappedEventName($event);
            } elseif ($adapter->hasMappedEventName($data->event)) {
                $event = $adapter->getMappedEventName($data->event);
            }
            if (is_callable($event)) {
                $event = $event($data);
            } else {
                $event = static::normaliseValue($event);
            }

            $properties = $adapter->eventPropertiesAs($data);
            if ($adapter->hasMappedEventProperty($event)) {
                $propertyAccess = $adapter->getMappedEventProperty($event);
            } elseif ($adapter->hasMappedEventProperty($data->event)) {
                $propertyAccess = $adapter->getMappedEventProperty($data->event);
            }

            if (isset($propertyAccess)) {
                if (is_callable($propertyAccess)) {
                    $properties = $propertyAccess($data);
                } elseif (is_string($propertyAccess)) {
                    if (method_exists($adapter, $propertyAccess)) {
                        $properties = $adapter->$propertyAccess($data);
                    } else {
                        $properties = Arr::get($properties, $propertyAccess);
                    }
                } elseif (is_array($propertyAccess)) {
                    $properties = $propertyAccess;
                }
            }

            $extraProperties = $adapter->extraProperties($data);
            $data->event = $event;
            $data->properties = array_merge($properties, $extraProperties);
        }

        return $data;
    }

    /**
     * Resolve model event name
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string  $method
     */
    public static function resolveModelEvent(\Illuminate\Database\Eloquent\Model|string $model, string $method)
    {
        $class = is_string($model) ? $model : get_class($model);

        return $class . '@' . $method;
    }

    public static function getDeviceData(Request $request): DeviceObject
    {
        $device = new DeviceObject();
        $browserInfo = new Browser();
        $osInfo = new Os();
        $deviceInfo = new Device();

        $device->url = $request->getUri();
        $device->browser = trim(str_replace('unknown', '', $browserInfo->getName() . ' ' . $browserInfo->getVersion()));
        $device->os = trim(str_replace('unknown', '', $osInfo->getName() . ' ' . $osInfo->getVersion()));
        $device->hardware = trim(str_replace('unknown', '', $deviceInfo->getName()));
        $device->referer = $request->header('referer');
        $device->refererDomain = ($request->header('referer')
            ? parse_url($request->header('referer'))['host']
            : null);
        $device->ip = $request->ip();

        if (!$device->browser && $browserInfo->isRobot()) {
            $device->browser = 'Robot';
        }

        return $device;
    }
}
