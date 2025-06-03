# Concepts

## Channels

**Channels** handle the delivery of events to various analytics tools or services.
You can register as many channels as needed in your configuration:

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

Once registered, you can send events to a specific channel by its key:

```php
Events::channel('channel-key')->track('my-event');
```

## Super properties

Super properties are key-value pairs automatically attached to every event.
These are useful for setting global context, such as app version or environment.

```php
Events::superProperties([
    'App Version' => App::version(),
    'Environment' => config('app.env'),
]);
```

> Note: This implementation may differ slightly from "super properties" in some analytics platforms — it's simply a shared event context in this package.

## Adapters

_Adapters_ allow you to intercept, transform, and remap events and their properties before they're dispatched to a channel.

This is especially useful when:

- Enforcing a naming convention across multiple services
- Adding or modifying fields for specific channels\
- Filtering or rewriting sensitive data

```php
Events::registerAdapter(new MyCustomAdapter());
```

> While the name "adapter" may differ from industry terms, think of it as a middleware layer for event transformation.
