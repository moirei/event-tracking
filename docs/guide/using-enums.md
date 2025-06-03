# Using Enums

If you use PHP Enums as a single source of truth for your application's constants, you can also use them as event names in this package.

For example, this package provides an `EcommerceEvent` enum that defines standard event names commonly accepted by services like **Google Analytics** and **Facebook Pixel**:

```php
use MOIREI\EventTracking\Enums\EcommerceEvent;

Events::track(EcommerceEvent::NEW_ORDER, $order);
```
