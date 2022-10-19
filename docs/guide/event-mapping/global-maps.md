# Global Event Maps

Sometimes it might be useful to map event name and properties globally.

```php
Events::mapEvent([
  resolve_model_event(Order::class, 'created') => 'Order Created',
  \Illuminate\Auth\Events\Login::class => 'Login',
  \Illuminate\Auth\Events\Registered::class => [
    'name' => 'Signup',
    'properties' => 'user',
  ],
]);
```

> Note that these mappings will always be overriden by any applied adapter transforms.

> resolve_model_event and other helpers are provided by this package.
