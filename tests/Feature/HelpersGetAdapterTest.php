<?php

use MOIREI\EventTracking\Adapters\EventAdapter;
use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

uses()->group('helpers', 'get-adapter');

it('should get adapter for channel', function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = EcommerceEvents::AddToCart->value;
    $eventPayload->properties = [];

    $adapter = Helpers::getAdapter(
        [\MOIREI\EventTracking\Adapters\GaAdapter::class],
        'ga',
        $eventPayload,
    );

    expect($adapter)->toBeInstanceOf(EventAdapter::class);
});

it('should return null for other channel', function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = EcommerceEvents::AddToCart->value;
    $eventPayload->properties = [];

    $adapter = Helpers::getAdapter(
        [\MOIREI\EventTracking\Adapters\GaAdapter::class],
        'unknown',
        $eventPayload,
    );

    expect($adapter)->toBeNull();
});

it('should return null for excluded event', function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = 'other-event';
    $eventPayload->properties = [];

    $adapter = Helpers::getAdapter(
        [\MOIREI\EventTracking\Adapters\GaAdapter::class],
        'ga',
        $eventPayload,
    );

    expect($adapter)->toBeNull();
});
