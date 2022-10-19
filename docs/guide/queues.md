# Queues

Tracked events can of course be processed as queued jobs in the background.

```php
// config/event-tracking.php

return [
    ...
    'queue' => [
        'connection' => 'redis',
        'queue' => 'default',
        'middleware' => [],
        'disabled' => false,
    ],
];
```

When queue is disabled, events are immediately processed.
