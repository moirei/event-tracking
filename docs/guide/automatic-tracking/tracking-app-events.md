# Tracking App Events

## Using TrackableEvent

Your application events that implement `TrackableEvent` will automatically be tracked.

```php
namespace App\Events;

use MOIREI\EventTracking\Contracts\TrackableEvent;

class PasswordUpdated implements TrackableEvent
{
    use Dispatchable;
    use SerializesModels;

    ...
}
```

## Auto tracking external events

Events defined in vendor files can be included via configuration.

```php
// connfig\event-tracking.php
...
'auto_tracking' => [
    ...
    'listen' => [
        \Illuminate\Auth\Events\Login::class,
        \Illuminate\Auth\Events\Registered::class,
    ],
],
```

Registered events may be mapped with different names or properties

```php
...
'auto_tracking' => [
    ...
    'listen' => [
        ...
        \Illuminate\Auth\Events\Login::class => 'Login',
        \Illuminate\Auth\Events\Registered::class => [
            'name' => 'User: Registered',
            'properties' => 'toArray',
        ],
    ],
],
```

## Manually registering events

```php
Events::listen(OrderPlaced::class);
Events::listen(OrderPlaced::class, ...);
Events::listen([
    OrderPlaced::class,
    ...
]);
```

```php
EventTracking::listen([
    OrderPlaced::class => 'Order: Placed'
]);

EventTracking::listen([
    OrderPlaced::class => [
        'name' => 'Order: Placed',
        'properties' => 'toArray',
    ]
]);
```
