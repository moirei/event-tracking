<?php

use Illuminate\Http\Request;
use MOIREI\EventTracking\EventAction;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\EventTrackingServiceProvider;

uses()->group('event-tracking-query');

beforeEach(function () {
    app()->register(EventTrackingServiceProvider::class);
    $request = new Request();
    $this->eventTracking = new EventTracking($request);
    $this->fakeChannel = [
        'handler' => \MOIREI\EventTracking\Channels\Mixpanel::class,
        'config' => [],
        'disabled' => false,
    ];
});

xit('expects eventTracking to extract device from request', function () {
    expect(1)->toEqual(1);
});

xit('expects EventTracking::track to use all channels', function () {
    $this->eventTracking->registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => $this->fakeChannel,
    ]);

    $this->eventTracking->track('testEvent');
});

xit('expects EventTracking::identify to use all channels', function () {
    $this->eventTracking->registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => $this->fakeChannel,
    ]);

    $this->eventTracking->identify('user-id');
});

it('expects EventTracking::all to use all channels', function () {
    $channels = [
        'channel-1' => $this->fakeChannel,
        'channel-2' => $this->fakeChannel,
    ];

    $this->eventTracking->registerChannel($channels);
    $eventAction = $this->eventTracking->all();

    expect($eventAction)->toBeInstanceOf(EventAction::class);
    expect(invade($eventAction)->channels)->toEqual(array_keys($channels));
});

it('expects EventTracking::all to use all channels except disabled channels', function () {
    $this->eventTracking->registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => array_merge($this->fakeChannel, ['disabled' => true]),
        'channel-3' => $this->fakeChannel,
    ]);

    $eventAction = $this->eventTracking->all();

    expect($eventAction)->toBeInstanceOf(EventAction::class);
    expect(invade($eventAction)->channels)->toHaveCount(2);
    expect(invade($eventAction)->channels)->toEqual(['channel-1', 'channel-3']);
});

it('expects EventTracking::except to use all enabled channels except specified channels', function () {
    $this->eventTracking->registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => array_merge($this->fakeChannel, ['disabled' => true]),
        'channel-3' => $this->fakeChannel,
    ]);

    $eventAction = $this->eventTracking->except('channel-3');

    expect($eventAction)->toBeInstanceOf(EventAction::class);
    expect(invade($eventAction)->channels)->toHaveCount(1);
    expect(invade($eventAction)->channels)->toEqual(['channel-1']);
});

it('expects EventTracking::only to use specified channels', function () {
    $this->eventTracking->registerChannel([
        'channel-1' => $this->fakeChannel,
        'channel-2' => array_merge($this->fakeChannel, ['disabled' => true]),
        'channel-3' => $this->fakeChannel,
    ]);

    $eventAction = $this->eventTracking->only('channel-3');

    expect($eventAction)->toBeInstanceOf(EventAction::class);
    expect(invade($eventAction)->channels)->toHaveCount(1);
    expect(invade($eventAction)->channels)->toEqual(['channel-3']);
});
