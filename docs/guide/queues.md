# Queues

Tracked events can be processed in the background using Laravel’s queue system.

## Configuration

To enable queuing, configure the `queue` section in your `config/event-tracking.php` file:

```php
return [
    // ...

    'queue' => [
        'connection' => 'redis',    // Laravel queue connection to use
        'queue' => 'default',       // Queue name
        'middleware' => [],         // Optional middleware for the queued job
        'disabled' => false,        // Set to true to disable queuing
    ],
];
```

## Behavior

- When disabled is set to false, events will be dispatched to the queue and processed asynchronously.
- When disabled is true, events will be processed immediately and synchronously.

This setup allows you to offload event tracking from the main request cycle, improving performance and reliability in production environments.
