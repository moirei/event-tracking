# Track Events

This package was created to really simplify sending both app and models events from a Laravel application to external analytics tools.

```php

use MOIREI\EventTracking\Facades\Events;

Events::track("New Order", $order);
```

## Identify

Identify and update a user profile

```php
// send only to mixpanel
Events::channel('mixpanel')->identify($user->id, [
    '$first_name'       => $user->first_name,
    '$last_name'        => $user->last_name,
    '$email'            => $user->email,
    '$phone'            => $user->phone,
    "Favorite Color"    => $user->favoriteColor,
]);
```

The `identify` utility does not currently support property remapping. The above example is for `Mixpanel`.

However, models that implement `EventUser` can directly provide core identification properties.

```php
...
use MOIREI\EventTracking\Contracts\EventUser;

class User extends Model implements EventUser{
    ...
}
```

```php
Events::identify($user);
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
