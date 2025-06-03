# Adapters

**Adapters** allow you to intercept events as they are sent to a channel and modify their names or payload properties.
This is especially useful for formatting data differently across platforms like **Mixpanel**, **Google Analytics**, or **BigQuery**.

## Example: Custom Adapter

```php
use Illuminate\Support\Arr;
use MOIREI\EventTracking\Adapters\EventAdapter;
use MOIREI\EventTracking\Objects\EventPayload;

class MyAdapter extends EventAdapter
{
    /**
     * Apply this adapter only to specific channels.
     *
     * Return `null` or omit to apply to all channels.
     */
    public function channels()
    {
        return ['mixpanel', 'ga'];
    }

    /**
     * Apply this adapter only to specific events.
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
     * Configure event name and property mappings.
     */
    public function configure()
    {
        // Rename individual events
        static::mapEvent(\Illuminate\Auth\Events\Login::class, 'Login');
        static::mapEvent(\Illuminate\Auth\Events\Logout::class, 'Logout');
        static::mapEvent(\Illuminate\Auth\Events\Registered::class, 'Signup');

        // Or use batch mapping
        static::mapEvents([
            \Illuminate\Auth\Events\Login::class => 'Login',
            \Illuminate\Auth\Events\Logout::class => 'Logout',
            \Illuminate\Auth\Events\Registered::class => 'Signup',
        ]);

        // Customize event properties
        static::mapEventProperty(
            \Illuminate\Auth\Events\Login::class,
            function (EventPayload $payload) {
                $user = Arr::get($payload->properties, 'user');
                // Process and return the transformed value
                return $user;
            }
        );
    }
}
```

In this example, only `Login`, `Logout`, and `Registered` events are renamed and modified when sent to `mixpanel` and `ga`.

## Mapping properties via method name

Instead of providing a closure, you can also pass a method name or property key:

```php
class MyAdapter extends EventAdapter
{
    public function configure()
    {
        static::mapEventProperty([
            \Illuminate\Auth\Events\Login::class,
            \Illuminate\Auth\Events\Logout::class,
            \Illuminate\Auth\Events\Registered::class,
        ], 'getUserProperty');
    }

    public function getUserProperty(EventPayload $payload)
    {
        $user = Arr::get($payload->properties, 'user');
        // Transform user data
        return $user;
    }
}
```

If the string provided is:

- A method name (like getUserProperty), it will be called.
- A property key (like 'user.email'), it will extract that value from the payload.

## Registering adapters

You can register adapters in your config:

```php
// config/event-tracking.php

'adapters' => [
    \App\EventTracking\MyAdapter::class,
],
```

Or register them manually (e.g., in a service provider’s `boot()` method):

```php
Events::registerAdapter(MyAdapter::class);
```

Adapters provide a powerful and clean way to centralize naming and formatting rules across all your analytics channels.
