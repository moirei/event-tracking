<?php

use Illuminate\Http\Request;
use MOIREI\EventTracking\EventHandler;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\EventTrackingServiceProvider;

uses()->group('event-hook');

beforeEach(function () {
    $request = new Request();
    $this->eventTracking = new EventTracking($request);
    app()->register(EventTrackingServiceProvider::class);
});

it('should add before hook', function () {
    $handler = fn () => null;
    EventTracking::before('test-event', $handler);
    $handlers = EventTracking::getEventHookHandlers('before', 'test-event');

    expect($handlers)->toHaveCount(1);
    expect($handlers[0])->toEqual($handler);
});

it('should run event hook', function () {
    /** @var object */
    $spy = \Mockery::spy('TestClass');

    EventTracking::before('test-event', function () use ($spy) {
        $spy->called();
    });

    EventHandler::runHooks('before', 'test-event', []);

    $spy->shouldHaveReceived()->called();
});

it('expect hooks to return true', function () {
    EventTracking::before('test-event', function () {
        //
    });

    $shouldRun = EventHandler::runHooks('before', 'test-event', []);

    expect($shouldRun)->toBeTrue();
});

it('expect hooks to return false when a handler resolves false', function () {
    EventTracking::before('test-event', function () {
        //
    });
    EventTracking::before('test-event', function () {
        return false;
    });

    $shouldRun = EventHandler::runHooks('before', 'test-event', []);

    expect($shouldRun)->toBeFalse();
});
