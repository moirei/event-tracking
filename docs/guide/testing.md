# Testing

We all love unit tests. It gives us that bit more confidence in what a code logic is actually doing. So it's reasonable to handle testing tracked events.

When writing tests that trigger events (this includes tests that fire app events that are indirectly tracked or model events being indirectly observed), you can fake the tracked events so they're not actually sent off to any registered channels.

```php
Events::fake();
```

Now all tracked events are instead stubbed and can be unit tested.

```php
Events::assertTrackedTimes(OrderPlaced::class, 2);
```

## Faking A Subset Of Events

You can fake tracked events for specific set of events using the `fake` or `fakeFor` method:

```php
Event::fake([
    OrderPlaced::class,
]);

event(new OrderPlaced($order));

Events::assertTracked(OrderPlaced::class);
```

## Scoped Fakes

You can use `fakeFor` if you only want to fake tracked events for a portion of your test:

```php
$order = Event::fakeFor(function () {
    $order = factory(Order::class)->create();

    event(new OrderPlaced($order));

    Events::assertTracked(OrderPlaced::class);

    return $order;
});
```
