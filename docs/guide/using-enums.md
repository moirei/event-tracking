# Using Enums

If you use Enums as single source of truth for your app constants, it's also possible to use them as event names.

This package provides `EcommerceEvent` Enum as an example for the basic ecommerce events accepted by `GA` and `Facebook Pixel`.

```php
Events::track(EcommerceEvent::NEW_ORDER, $order);
```
