<?php

use MOIREI\EventTracking\Adapters\GaAdapter;
use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

uses()->group('adapters', 'ga-adapter');

beforeEach(function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = EcommerceEvents::AddToCart->value;
    $eventPayload->properties = [];

    $this->adapter = Helpers::getAdapter(
        [GaAdapter::class],
        'ga',
        $eventPayload,
    );
});

it('should create instance', function () {
    /** @var GaAdapter */
    $adapter = $this->adapter;
    expect($adapter)->toBeInstanceOf(GaAdapter::class);
});
