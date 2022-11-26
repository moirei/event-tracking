<?php

use Illuminate\Auth\Events\Login;
use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Facades\Events;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Testing\EventTrackingFake;

uses()->group('fake-event-tracking-test');

beforeEach(function () {
    app()->register(EventTrackingServiceProvider::class);

    $this->fakeChannel = [
        'handler' => \MOIREI\EventTracking\Channels\Mixpanel::class,
        'config' => [],
        'disabled' => false,
    ];
});

it('expect except when attempting assert without faker', function () {
    $this->expectException(\Error::class);
    Events::assertTracked('my-event');
});

it('should replace facade', function () {
    expect(Events::getFacadeRoot())->toBeInstanceOf(EventTracking::class);
    expect(Events::getFacadeRoot())->not->toBeInstanceOf(EventTrackingFake::class);

    Events::fake();

    expect(Events::getFacadeRoot())->toBeInstanceOf(EventTrackingFake::class);
});

it('expect faker to not capture unfired event', function () {
    Events::fake();
    Events::assertNotTracked('my-event');
});

it('expect faker to capture fired event', function () {
    Events::fake();
    Events::track('my-event');
    Events::assertTracked('my-event');
});

it('expect faker to capture fired enum event', function () {
    Events::fake();
    Events::track(EcommerceEvents::AddToCart);
    Events::assertTracked(EcommerceEvents::AddToCart);
});

it('expect faker to capture fired event on channels', function () {
    Events::fake();

    Events::registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => $this->fakeChannel,
    ]);

    Events::on('channel-1', 'channel-2')->track('my-event');
    Events::assertTrackedOnChannels('my-event', 'channel-1');
    Events::assertTrackedOnChannels('my-event', ['channel-1', 'channel-2']);
    Events::assertNotTrackedOnChannels('my-event', 'channel-4');
    Events::assertNotTrackedOnChannels('my-event', ['channel-1', 'channel-4']);
});

it('should accept callback for checking assertions', function () {
    Events::fake();

    Events::superProperties([
        'test-super-prop' => 1,
    ]);
    Events::track('my-event-1');

    Events::assertTracked('my-event-1', function ($event, EventPayload $payload) {
        expect($event)->toEqual('my-event-1');
        expect($payload)->toBeInstanceOf(EventPayload::class);
        expect($payload->properties)->toHaveKey('test-super-prop');
        return true; // assertion condition
    });

    Events::assertNotTracked('my-event-1', function ($event, EventPayload $payload) {
        expect($event)->toEqual('my-event-1');
        expect($payload)->toBeInstanceOf(EventPayload::class);
        return false; // assertion condition
    });
});

// xit('should accept callback for checking assertions', function () {
//     Events::fake();

//     Events::track(Login::class);

//     Events::assertTracked(function (Login $event) {
//         return true;
//     });
// });

it('expect faker to capture multiple fired events', function () {
    Events::fake();

    Events::track('my-event-1');
    Events::track('my-event-2');
    Events::track('my-event-2');
    Events::track('my-event-3');
    Events::track('my-event-3');
    Events::track('my-event-3');

    Events::assertTrackedTimes('my-event-1');
    Events::assertTrackedTimes('my-event-2', 2);
    Events::assertTrackedTimes('my-event-3', 3);
});

it('expect faker to capture multiple fired events on channels', function () {
    Events::fake();

    Events::registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => $this->fakeChannel,
    ]);

    Events::on('channel-1', 'channel-2')->track('my-event-1');

    Events::on('channel-1', 'channel-2')->track('my-event-2');
    Events::on('channel-1', 'channel-2')->track('my-event-2');

    Events::on('channel-1', 'channel-2')->track('my-event-3');
    Events::on('channel-1', 'channel-2')->track('my-event-3');
    Events::on('channel-1', 'channel-2')->track('my-event-3');

    Events::assertTrackedTimesOnChannels('my-event-1', 'channel-1');
    Events::assertTrackedTimesOnChannels('my-event-1', ['channel-1', 'channel-2']);

    Events::assertTrackedTimesOnChannels('my-event-2', 'channel-1', 2);
    Events::assertTrackedTimesOnChannels('my-event-2', ['channel-1', 'channel-2'], 2);

    Events::assertTrackedTimesOnChannels('my-event-3', 'channel-1', 3);
    Events::assertTrackedTimesOnChannels('my-event-3', ['channel-1', 'channel-2'], 3);
});

it('should only fake all events by default', function () {
    Events::fake();

    $eventAction = Events::all();

    expect(invade($eventAction)->shouldFakeEvent('my-event-1', []))->toBeTrue();
    expect(invade($eventAction)->shouldFakeEvent('my-event-2', []))->toBeTrue();
});

it('should only fake provided events', function () {
    Events::fake('my-event-1');

    $eventAction = Events::all();

    expect(invade($eventAction)->shouldFakeEvent('my-event-1', []))->toBeTrue();
    expect(invade($eventAction)->shouldFakeEvent('my-event-2', []))->toBeFalse();
});

it('expect fakeFor to restore bound instance', function () {
    expect(Events::getFacadeRoot())->toBeInstanceOf(EventTracking::class);

    $value = Events::fakeFor(function () {
        expect(Events::getFacadeRoot())->toBeInstanceOf(EventTrackingFake::class);
        return 1;
    });

    expect(get_class(Events::getFacadeRoot()))->toEqual(EventTracking::class); // direct instance
    expect(Events::getFacadeRoot())->not->toBeInstanceOf(EventTrackingFake::class);
    expect($value)->toEqual(1);
});
