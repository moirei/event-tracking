<?php

namespace MOIREI\EventTracking;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use MOIREI\EventTracking\Contracts\EventUser;
use MOIREI\EventTracking\Contracts\EventUserProxy;
use MOIREI\EventTracking\Listeners\TrackableEventListener;
use MOIREI\EventTracking\Objects\Device;
use MOIREI\EventTracking\Objects\User;
use MOIREI\EventTracking\Observers\ModelObserver;

class EventTracking
{
    protected Device $device;

    protected static $adapters = [];

    protected static $hooks = [];

    protected $channels = [];

    protected static $globalEventMap = [];

    protected $instanceCache = [];

    public function __construct(Request $request)
    {
        $this->device = Helpers::getDeviceData($request);
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
     * @param  EventUser|EventUserProxy|User|string  $user
     * @param  array  $properties
     */
    public function identify(EventUser|EventUserProxy|User|string $user, array $properties = [])
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
     * Alias of only().
     * Send event to only named channels.
     *
     * @param  string|array<string>  $channels
     * @return EventAction
     */
    public function on($channels): EventAction
    {
        $channels = is_array($channels) ? $channels : func_get_args();
        return $this->only($channels);
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
            $model::observe(ModelObserver::make($options));
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
     * Register event adapters.
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
     * Get registered event adapters.
     *
     * @return string[]
     */
    public static function getAdapters(): array
    {
        return static::$adapters;
    }

    /**
     * Register event channels.
     *
     * @param  array  $channels
     */
    public function registerChannel(array $channels)
    {
        $this->channels = array_merge($this->channels, $channels);
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
     * Get globally mapped events.
     *
     * @return  array<string, string|\Closure>
     */
    public static function getEventMaps()
    {
        return static::$globalEventMap;
    }

    /**
     * Register a before hook
     *
     * @param  mixed  $events
     * @param  Closure  $handler
     */
    public static function before(mixed $events, Closure $handler)
    {
        static::addEventHook('before', $events, $handler);
    }

    /**
     * Register a after hook
     *
     * @param  mixed  $events
     * @param  Closure  $handler
     */
    public static function after(mixed $events, Closure $handler)
    {
        static::addEventHook('after', $events, $handler);
    }

    /**
     * Add a new hook for events.
     *
     * @param  string  $name
     * @param  mixed  $events
     * @param  Closure  $handler
     */
    protected static function addEventHook(string $name, mixed $events, Closure $handler)
    {
        $events = is_array($events) ? $events : [$events];
        foreach ($events as $event) {
            $event = Helpers::normaliseValue($event);
            $handlers = Arr::get(static::$hooks, "$event.$name", []);
            $handlers[] = $handler;
            Arr::set(static::$hooks, "$event.$name", $handlers);
        }
    }

    /**
     * Get registered hook handlers for event.
     *
     * @param  string  $hook
     * @param  mixed  $event
     * @return Closure[]
     */
    public static function getEventHookHandlers(string $hook, $event): array
    {
        $event = Helpers::normaliseValue($event);

        return Arr::get(static::$hooks, "$event.$hook", []);
    }

    /**
     * Set super properties.
     *
     * @param  array<string, string|\Closure>  $map
     */
    public function superProperties(array $map)
    {
        $this->setCache('$superProperties', array_merge($this->getCache('$superProperties', []), $map));
    }

    /**
     * Get registered super properties.
     *
     * @return  array<string, string|int>
     */
    public function getSuperProperties()
    {
        return $this->getCache('$superProperties', []);
    }

    /**
     * Get/set active user.
     *
     * @param  User|EventUser|EventUserProxy  $user
     * @return  User|null
     */
    public function user(User|EventUser|EventUserProxy $user = null): User|null
    {
        if ($user) {
            if ($user instanceof EventUserProxy) {
                $user = $user->getEventUser();
            } elseif ($user instanceof EventUser) {
                $userObject = new User();
                $userObject->id = $user->getId();
                $userObject->name = $user->getName();
                $userObject->firstName = $user->getFirstName();
                $userObject->lastName = $user->getLastName();
                $userObject->email = $user->getEmail();
                $userObject->createdAt = $user->getCreatedDate();
                $user = $userObject;
            }
            $this->setCache('$user', $user);
        }

        return $this->getCache('$user');
    }

    /**
     * Get a registered event channel.
     *
     * @param  array<string, string|\Closure>  $map
     * @return \MOIREI\EventTracking\Channels\EventChannel
     *
     * @throws \InvalidArgumentException
     */
    public function getChannel(string $channel)
    {
        // instance is not cached so that they can be auto
        // destroyed in queues.

        $config = Arr::get($this->channels, $channel);
        if (!$config) {
            throw new \InvalidArgumentException("Unknown channel $channel");
        }
        $options = $config['config'];
        if (is_string($options)) {
            $options = config($options, []);
        }
        /** @var \MOIREI\EventTracking\Channels\EventChannel */
        $instance = app($config['handler']);
        $instance->initialize($options);

        return $instance;
    }

    /**
     * Cache a miscellaneous value.
     *
     * @param  string  $key
     * @param  mixed  $value
     */
    public function setCache($key, $value)
    {
        $this->instanceCache[$key] = $value;
    }

    /**
     * Get a cached key value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getCache($key, $default = null)
    {
        return array_key_exists($key, $this->instanceCache) ? $this->instanceCache[$key] : $default;
    }

    /**
     * Create a new event action.
     *
     * @param  array  $channels
     * @return EventAction
     */
    public function makeEvent(array $channels): EventAction
    {
        return new EventAction($channels, $this->device);
    }

    /**
     * @return string[]
     */
    protected function channels()
    {
        $channels = [];
        foreach ($this->channels as $name => $options) {
            if (!Arr::get($options, 'disabled')) {
                $channels[] = $name;
            }
        }

        return $channels;
    }
}
