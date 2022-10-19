# Event Tracking

This package allows you to send application events to analytics services and data-warehouse. Inspired by [Analytics.io](https://github.com/DavidWells/analytics).

## Documentation

All documentation is available at [the documentation site](https://moirei.github.io/event-tracking).

## Features

- Send events to multiple analytics channels
- Flexible event name and property mapping per channel per event
- Automatically capture and track local and vendor events
- Capture and track observable model events
- Use PHP Enums as event names
- Send events asynchronously queues

## Example

```php
Events::track('Purchase', $order);

// or auto track app events

// Somewhere in your app
Events::listen(OrderPlacedEvent::class);

// Elsewhere in your app
OrderPlacedEvent::dispatch($order);
```

## Installation

```bash
composer require moirei/event-tracking
```

Publish the config

```bash
php artisan vendor:publish --tag="event-tracking"
```

## Tests

```bash
composer test
```
