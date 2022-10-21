# Adapters

Adapters make it possible to tap into any given channel and event in order to modify the event name or payload properties.

For example if you send events to `Mixpanel`, `GA`, and `BigQuery`, you can format certain events going to `Mixpanel` and `GA`.

```php
use MOIREI\EventTracking\Adapters\Adapters;

class MyAdapter extends EventAdapter
{
    /**
     * Omit to apply to all channels.
     *
     * {@inheritdoc}
     */
    public function channels()
    {
        return [
          'mixpanel', 'ga',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function only()
    {
        return [
            \Illuminate\Auth\Events\Login::class,
            \Illuminate\Auth\Events\Logout::class,
            \Illuminate\Auth\Events\Registered::class,
        ];
    }

    /**
     * Map event properties.
     *
     * @param  array<string, \Closure>  $map
     */
    public function configure()
    {
        static::mapEvent(Illuminate\Auth\Events\Login::class, 'Login');
        static::mapEvent(Illuminate\Auth\Events\Logout::class, 'Logout');
        static::mapEvent(Illuminate\Auth\Events\Registered::class, 'Signup');

        // or

        static::mapEvents([
            \Illuminate\Auth\Events\Login::class => 'Login',
            \Illuminate\Auth\Events\Logout::class => 'Logout',
            \Illuminate\Auth\Events\Registered::class => 'Signup',
        ]);

        // map event property

        static::mapEventProperty(
            \Illuminate\Auth\Events\Login::class,
            function (EventPayload $payload) {
                $user = Arr::get($payload->properties, 'user');
                ...
                return $user;
            }
        );
    }
}
```

In this case only `Login`, `Logout` and `Registered` events are renamed by this adapter.

When mapping event properties, it's possible to register multiple events at once. You can also pass a string as a value. If the there is an existing public method then it's called to retrieve the mapped properties value; if not, then the value is used as key to pick the existing properties.

```php
class MyAdapter extends EventAdapter
{
    ...

    public function configure()
    {
        ...

        static::mapEventProperty([
            \Illuminate\Auth\Events\Login::class,
            \Illuminate\Auth\Events\Logout::class,
            \Illuminate\Auth\Events\Registered::class,
        ], 'getUserProperty');
    }

    public function getUserProperty(EventPayload $payload){
        $user = Arr::get($payload->properties, 'user');
        ...
        return $user;
    }
}
```

You may register adapters in config;

```php
// config/event-tracking.php

return [
    ...
    'adapters' => [
        \App\EventTracking\MyAdapter::class,
    ],
];
```

Or manually (ideally in the boot method of a service provider),

```php
Events::registerAdapter(MyAdapter::class);
```
