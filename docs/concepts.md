# Concepts

## Channels

Event channels handle sending events to different analytics tools. You can register as many channels
as you need to send events to remote services.

```php
// config/event-tracking.php

return [
    ...
    'channels' => [
        'channel-key' => [
            'handler' => \App\EventTracking\MyChannel::class,
            ...
        ],
    ],
];
```

Once registered, channels can be keyed

```php
Events::channel('channel-key')->track('my-event');
```

## Super properties

The concept of super properties in this package may not be consistent with what you might be familiar with.
Super properties are just a way to declare properties that are associated with every event.

```php
Events::superProperties([
    'App Version' => App::version(),
]);
```

## Adapters

Adapters (once again may not be an ideal naming for the concept) intercept and remap events and/or preperties.
This is powerful for various use cases, especially when enforcing event naming convention accross multiple channels on select events.
