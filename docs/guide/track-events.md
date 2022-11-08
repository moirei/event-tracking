# Track Events

This package was created to really simplify sending both app and models events from a Laravel application to external analytics services.

```php

use MOIREI\EventTracking\Facades\Events;

Events::track("New Order", $order);
```

> Declaring the Events facade is optional.

## Channels

This package comes with in-built channels for `Mixpanel` and `GA`.

> Neither of these channels are unit tested at this time. However, `Mixpanel` is fully production ready.

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

### Send event to all channels except a few channels

```php
Events::except('mixpanel', 'ga')->track('my-event');
// or
Events::except(['mixpanel', 'ga'])->track('my-event');
```
