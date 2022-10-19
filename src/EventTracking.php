<?php

namespace MOIREI\EventTracking;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Listeners\TrackableEventListener;
use MOIREI\EventTracking\Objects\Device as DeviceObject;
use MOIREI\EventTracking\Observers\ModelObserver;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Device;
use Sinergi\BrowserDetector\Os;

class EventTracking
{
    protected DeviceObject $device;

    public static $adapters = [];

    public static $globalEventMap = [];

    public function __construct(Request $request)
    {
        $this->device = $this->getDeviceData($request);
    }

    /**
     * Track an event.
     *
     * @param $event
     * @param    $properties
     */
    public function track($event, $properties = [])
    {
        return $this->all()->track($event, $properties);
    }

    /**
     * Identify a user.
     *
     * @param  EventUser|string  $user
     * @param  array  $properties
     */
    public function identify(EventUser|string $user, array $properties = [])
    {
        return $this->all()->identify($user, $properties);
    }

    /**
     * Send event to all channels.
     *
     * @return EventAction
     */
    public function all(): EventAction
    {
        return $this->makeEvent($this->channels());
    }

    /**
     * Send event to all channels except named channels.
     *
     * @param  string|array<string>  $channels
     * @return EventAction
     */
    public function except($channels): EventAction
    {
        $allChannels = $this->channels();
        $except = is_array($channels) ? $channels : func_get_args();
        $channels = array_filter($allChannels, function ($channel) use ($except) {
            return !in_array($channel, $except);
        });

        return $this->makeEvent(array_values($channels));
    }

    /**
     * Send event to only named channels.
     *
     * @param  string|array<string>  $channels
     * @return EventAction
     */
    public function only($channels): EventAction
    {
        $allChannels = $this->channels();
        $only = is_array($channels) ? $channels : func_get_args();
        $channels = array_filter($allChannels, function ($channel) use ($only) {
            return in_array($channel, $only);
        });

        return $this->makeEvent(array_values($channels));
    }

    /**
     * Send event to only named channel.
     *
     * @param  string  $channel
     * @return EventAction
     */
    public function channel(string $channel): EventAction
    {
        return $this->only($channel);
    }

    /**
     * Observe model events.
     *
     * @param  mixed  $models
     */
    public static function observe(mixed $models)
    {
        $models = is_array($models) ? $models : func_get_args();
        foreach ($models as $key => $value) {
            if (is_array($value)) {
                $model = $key;
                $options = $value;
            } else {
                $model = $value;
                $options = [];
            }
            $model::observe(new ModelObserver($options));
        }
    }

    /**
     * Listen to events.
     *
     * @param  mixed  $events
     */
    public static function listen(mixed $events)
    {
        $listen = is_array($events) ? $events : func_get_args();
        $events = [];
        $mapEvent = [];

        foreach ($listen as $key => $value) {
            if (is_numeric($key)) {
                $events[] = $value;
            } else {
                $events[] = $key;
                if (is_string($value)) {
                    $value = ['name' => $value];
                }
                $mapEvent[$key] = $value;
            }
        }

        static::mapEvent($mapEvent);
        Event::listen($events, TrackableEventListener::class);
    }

    /**
     * Register model adapters.
     *
     * @param  mixed  $adapters
     */
    public static function registerAdapter(mixed $adapters)
    {
        $adapters = is_array($adapters) ? $adapters : func_get_args();
        static::$adapters = array_unique(
            array_merge(static::$adapters, $adapters)
        );
    }

    /**
     * Map event name and properties globally.
     *
     * @param  array<string, string|\Closure>  $map
     */
    public static function mapEvent(array $map)
    {
        static::$globalEventMap = array_merge(static::$globalEventMap, $map);
    }

    /**
     * @return string[]
     */
    protected function channels()
    {
        $channels = [];
        foreach (config('event-tracking.channels') as $name => $options) {
            if (!Arr::get($options, 'disabled')) {
                $channels[] = $name;
            }
        }

        return $channels;
    }

    protected function getDeviceData(Request $request): DeviceObject
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

    protected function makeEvent(array $channels): EventAction
    {
        return new EventAction($channels, $this->device);
    }
}