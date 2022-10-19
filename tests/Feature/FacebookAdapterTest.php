<?php

use MOIREI\EventTracking\Adapters\FacebookAdapter;
use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

uses()->group('adapters', 'facebook-adapter');

beforeEach(function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = EcommerceEvents::AddToCart->value;
    $eventPayload->properties = [];

    $this->adapter = Helpers::getAdapter(
        [FacebookAdapter::class],
        'facebook',
        $eventPayload,
    );
});

it('should create instance', function () {
    /** @var FacebookAdapter */
    $adapter = $this->adapter;
    expect($adapter)->toBeInstanceOf(FacebookAdapter::class);
});
