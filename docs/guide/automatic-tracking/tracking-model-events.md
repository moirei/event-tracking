# Tracking Model Events

Tracking event models is possible via an internal observer with a few tricks. You can provide detailed options when configurting or registering an observable model.

## Observable options

By default, only `created`, `updated` and `restored` events are observed. When new keys are defined, they're automatically included.

```php
Events::observe([
    \App\Models\User::class => [
        'deleted' => 'User Deleted',
    ]
]);
```

Other options:

| Option    | Description                                             | Type       |
| --------- | ------------------------------------------------------- | ---------- |
| `$all`    | Observe all currently known events                      | `boolean`  |
| `$only`   | Observe only select events                              | `string[]` |
| `$except` | Observe all currently known events except select events | `string[]` |

## Auto tracking model events

```php
// connfig\event-tracking.php
...
'auto_tracking' => [
    ...
    'observe' => [
        \App\Models\User::class,
    ],
],
```

Registered model events may be mapped with different names or properties

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

```php
Events::observe(\App\Models\User::class);
// or
Events::observe(\App\Models\User::class, ...);
// or
Events::observe([
    \App\Models\User::class,
    ...
]);
// or
Events::observe([
    \App\Models\User::class => [
        ...
    ]
]);
```

## Using TrackableModel

Your models that implement `TrackableModel` can redirect provide event names and properties.

> Note that models that implement TrackableModel are not automatically tracked.

```php
namespace App\Events;

use MOIREI\EventTracking\Contracts\TrackableModel;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements TrackableEvent
{
    ...
}
```
