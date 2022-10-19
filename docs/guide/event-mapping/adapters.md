# Adapters

Adapters make it possible to tab into any given channel and event in order to modify the event name or payload properties.

For example if you send events to `Mixpanel`, `GA`, and `BigQuery`, you can format certain events going to `Mixpanel` and `GA`.

```php
use MOIREI\EventTracking\Adapters\Adapters;

class MyAdapter extends EventAdapter
{
    /**
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
            \Illuminate\Auth\Events\Registered::class,
        ];
    }

    /**
     * Map event properties.
     *
     * @param  array<string, \Closure>  $map
     */
    public static function configure()
    {
        static::mapEvent(Illuminate\Auth\Events\Registered::class, 'Signup');
    }
}
```

In this case only the `Registered::class` event is renamed.

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
