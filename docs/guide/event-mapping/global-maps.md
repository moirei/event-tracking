# Global Event Maps

Sometimes it's useful to define global event name and property mappings that apply across all channels.

This is especially helpful when you want consistent naming or payload transformations without writing a full adapter.

```php
use App\Models\Order;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;

Events::mapEvent([
    resolve_model_event(Order::class, 'created') => 'Order Created',
    Login::class => 'Login',
    Registered::class => [
        'name' => 'Signup',
        'properties' => 'user',
    ],
]);

> ℹ️ resolve_model_event() and related helpers are provided by this package to help you map model lifecycle events (e.g. created, updated, deleted).
```

## ⚠️ Adapter override behavior

Note that:

- Global mappings can be overridden by any adapter-specific mappings.
- Adapters take precedence over global mappings when both are applied.

Use global maps for base-level defaults, and adapters when you need channel- or event-specific customizations.
