# Hooks

Hooks can be used to perform important operations before and after an event is processed.
One or multiple events can be registered at once.

## Before hook

In the example below, we can track a login event against the actual user as they may not have been identified just yet.

```php
Events::before(Login::class, function(EventPayload $payload, array $originalProperties, array $channels){
  Events::user($originalProperties['user']);
});
```

If the callback returns `false`, the event is not processed.

## After hook

Likewise for after an event has been processed.

```php
Events::after([
  \Illuminate\Auth\Events\Logout::class,
  \Illuminate\Auth\Events\Lockout::class,
], function(EventPayload $payload, array $channels){
  // do something
});
```
