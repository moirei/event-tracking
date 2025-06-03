# Hooks

**Hooks** allow you to execute custom logic **before or after** an event is processed and sent to analytics channels.

You can register hooks for one or multiple events.

## Before hook

Use the `before()` hook to modify or prepare data **before** the event is dispatched.

For example, you can identify the user associated with a login event before it's tracked:

```php
use Illuminate\Auth\Events\Login;
use MOIREI\EventTracking\Objects\EventPayload;

Events::before(Login::class, function (EventPayload $payload, array $originalProperties, array $channels) {
    Events::user($originalProperties['user']);
});
```

> If the callback returns false, the event will be cancelled and not tracked.

## After hook

Use the `after()` hook to run logic after the event has been tracked:

```php
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Lockout;

Events::after([
    Logout::class,
    Lockout::class,
], function (EventPayload $payload, array $channels) {
  // Perform any post-tracking operations
});
```

## Use cases for hooks

- Set or override user identity (before)
- Cancel or suppress specific events conditionally (before)
- Log or audit after dispatch (after)
- Trigger related business logic (after)

Hooks give you fine-grained control over how and when events are tracked.
