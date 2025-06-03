# Testing

Unit tests give us confidence in how code behaves — and that includes event tracking.

When writing tests that trigger events (either directly or indirectly through application or model events), you can **fake** tracked events so they are not actually sent to external analytics channels.

## Basic usage

Use `Events::fake()` to intercept and stub all tracked events:

```php
Events::fake();

// Your application logic that triggers tracked events
event(new OrderPlaced($order));

// Assert that the event was tracked
Events::assertTrackedTimes(OrderPlaced::class, 2);
```

## Faking a subset of events

To fake only a specific set of events:

```php
Events::fake([
    OrderPlaced::class,
]);

event(new OrderPlaced($order));

Events::assertTracked(OrderPlaced::class);
```

## Scoped Fakes

Use `fakeFor()` if you want to fake tracked events for only part of a test.
All assertions and fakes will be scoped to that block:

```bash
$order = Events::fakeFor(function () {
    $order = factory(Order::class)->create();

    event(new OrderPlaced($order));

    Events::assertTracked(OrderPlaced::class);

    return $order;
});
```

This ensures other parts of your test are unaffected by the fake.

## Benefits of faking events

- Prevents real API calls to analytics tools during tests
- Improves test performance
- Enables accurate assertions on event behavior
- Works seamlessly with Laravel’s native testing tools

Event tracking becomes testable, predictable, and safe.
