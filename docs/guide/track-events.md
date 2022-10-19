# Track Events

This package was created to really simplify sending both app and models events from a Laravel application to external analytics tools.

```php

use MOIREI\EventTracking\Facades\Events;

Events::track("New Order", $order);
```

## Channels

Event channels handle sending events to different analytics tools.

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

> Neither of these channels where properly tested at this time.

### Send event to all avaialble channels

```php
Events::all()->track('my-event');
```

### Send event to only a few channels

```php
Events::only('mixpanel', 'ga')->track('my-event');
// or
Events::only(['mixpanel', 'ga'])->track('my-event');
```

### Send event to all channels except the given channels

```php
Events::except('mixpanel', 'ga')->track('my-event');
// or
Events::except(['mixpanel', 'ga'])->track('my-event');
```
