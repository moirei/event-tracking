# Event Tracking

This package allows you to send application events to analytics services and any data-warehouse of your choice. Although inspired by [Analytics.io](https://github.com/DavidWells/analytics) and [Segment](https://segment.com), it should provide you more flexibility over your events and how they're manually or automatically handled.

## Documentation

All documentation is available at [the documentation site](https://moirei.github.io/event-tracking).

## Features

- Send events to multiple analytics channels with ease
- Flexible event name and property mapping per channel per event
- Automatically capture and track local and vendor events
- Capture and track observable model events
- Use PHP Enums as event names
- Send events asynchronously with queues

## Example

Track an event

```php
Events::track('Purchase', $order);

// or auto track app events

// Somewhere in your app
Events::listen(OrderPlacedEvent::class);

// Elsewhere in your app
OrderPlacedEvent::dispatch($order);
```

Identify and update a user profile

```php
Events::identify($user->id, [
    'name'             => $user->name,
    'email'            => $user->email,
    'phone'            => $user->phone,
    'city'             => $user->city,
    'country'          => $user->country,
    "Favorite Color"   => $user->favoriteColor,
]);
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
