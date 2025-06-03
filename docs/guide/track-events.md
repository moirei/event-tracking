# Track Events

This package simplifies sending both application and model events from your Laravel app to external analytics services.

```php

use MOIREI\EventTracking\Facades\Events;

Events::track("New Order", $order);
```

> Importing the `Events` facade is optional if you're using global aliases.

## Channels

The package includes built-in support for `Mixpanel` and `Google Analytics (GA)`.

> ⚠️ Note: These channels are not currently unit tested. However, `Mixpanel` is production-ready and stable.

### Send to all active channels

```php
Events::all()->track('my-event');
```

### Send to specific channels

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

These fluent methods give you full control over which analytics channels receive which events.
