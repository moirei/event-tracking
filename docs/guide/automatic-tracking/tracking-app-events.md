# Tracking Application Events

This package allows you to automatically track application-level events through simple interfaces and flexible configuration.

## Using `TrackableEvent`

Any of your custom application events that implement the `TrackableEvent` interface will be automatically tracked:

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

## Auto-tracking external events

You can also auto-track vendor events (like Laravel Auth events) by specifying them in your config:

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

## Mapping event names and properties

You can rename events or specify how to extract properties using a callback or method reference:

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

Use `listen()` to manually register one or more events for tracking:

```php
Events::listen(OrderPlaced::class);
Events::listen(OrderPlaced::class, ...);
Events::listen([
    OrderPlaced::class,
    ...
]);
```

You may also use the service instance directly:

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

## Disabling auto-tracking

To fully disable auto-tracking via configuration:

```php
...
'auto_tracking' => [
    ...
    'disabled' => true,
],
```

To disable only when running in console (Artisan or queued jobs):

```php
...
'auto_tracking' => [
    ...
    'disable_in_console' => true,
],
```

## Temporarily disabling auto-tracking

To skip auto-tracking for a specific operation or block of logic:

```php
Events::withoutAutoTracking(function(){
    // Perform operations that should not be tracked
});
```

This will automatically restore the previous tracking state once the block completes.
