# Tracking Model Events

This package supports tracking Eloquent model events using an internal observer system.
You can configure tracking behavior with fine-grained control per model and event.

## Observable options

By default, the package tracks the following Eloquent events:

- `created`
- `updated`
- `restored`

To observe additional events or customize names, provide a mapping like this:

```php
Events::observe([
    \App\Models\User::class => [
        'deleted' => 'User Deleted',
    ]
]);
```

You may also use special options for broader control:

| Option    | Description                          | Type       |
| --------- | ------------------------------------ | ---------- |
| `$all`    | Observe all known Eloquent events    | `boolean`  |
| `$only`   | Only observe specified events        | `string[]` |
| `$except` | Observe all except the listed events | `string[]` |

## Auto tracking model events

You can register observable models directly in your config file:

```php
// config\event-tracking.php
...
'auto_tracking' => [
    ...
    'observe' => [
        \App\Models\User::class,
    ],
],
```

You can also customize how each model event is tracked:

```php
...
'auto_tracking' => [
    ...
    'observe' => [
        \App\Models\User::class => [
            'created' => [
                'name' => 'User: Registered',
                'properties' => 'toArray', // property or method name
            ],
            'deleted' => 'User: Deactivated',
        ],
    ],
],
```

## Manually observe model events

You may register model observers dynamically at runtime:

```php
Events::observe(\App\Models\User::class);

// With custom options:
Events::observe(\App\Models\User::class, ...);

// Multiple models:
Events::observe([
    \App\Models\User::class,
    ...
]);

// With options per model:
Events::observe([
    \App\Models\User::class => [
        ...
    ]
]);
```

## Using `TrackableModel`

If you want a model to define its own tracking logic, implement the `TrackableModel` interface.
This gives you control over event names and properties programmatically.

> Note: Implementing `TrackableModel` does not automatically enable tracking — you still need to register the model via `observe()` or the config.

```php
namespace App\Events;

use MOIREI\EventTracking\Contracts\TrackableModel;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements TrackableEvent
{
    ...
}
```

This is useful when event tracking logic depends on runtime conditions or model data.
