<?php

use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

uses()->group('helpers', 'apply-adapter-transform');

it('should transform event name with google adapter', function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = EcommerceEvents::AddToCart->value;
    $eventPayload->properties = [];

    $data = Helpers::applyAdapterTransform(
        [\MOIREI\EventTracking\Adapters\GaAdapter::class],
        'ga',
        $eventPayload,
    );

    expect($data->event)->toEqual('add_to_cart');
    expect($data->properties)->toHaveKeys(['id', 'value', 'currency', 'shipping']);
});

it('should transform event properties with google adapter', function () {
    $eventPayload = new EventPayload();
    $eventPayload->event = EcommerceEvents::AddToCart->value;
    $eventPayload->properties = [];

    $data = Helpers::applyAdapterTransform(
        [\MOIREI\EventTracking\Adapters\GaAdapter::class],
        'ga',
        $eventPayload,
    );

    expect($data->properties)->toHaveKeys(['id', 'value', 'currency', 'shipping']);
});
