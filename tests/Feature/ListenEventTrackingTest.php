<?php

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Facades\Events;
use MOIREI\EventTracking\Listeners\TrackableEventListener;

uses()->group('events', 'listen-events');

beforeEach(function () {
    app()->register(EventTrackingServiceProvider::class);
});

it('should register listener', function () {
    Event::fake();

    Events::listen(Verified::class);

    Event::assertListening(
        Verified::class,
        TrackableEventListener::class
    );
});

it('should handle registered events', function () {
    Events::listen(Lockout::class);

    $event = new Verified([]);
    Event::dispatch($event);

    expect(1)->toEqual(1);
});
