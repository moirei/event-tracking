<?php

use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;

uses()->group('helpers', 'normalise-value');

it('should normalise int to string', function () {
    $x = Helpers::normaliseValue(1);
    expect($x)->toBeString();
    expect($x)->toEqual('1');
});

it('should normalise string to string', function () {
    $x = Helpers::normaliseValue('1');
    expect($x)->toBeString();
    expect($x)->toEqual('1');
});

it('should normalise enum to string', function () {
    $x = Helpers::normaliseValue(EcommerceEvents::AddToCart);
    expect($x)->toBeString();
    expect($x)->toEqual(EcommerceEvents::AddToCart->value);
});

it('should normalise array', function () {
    $x = Helpers::normaliseValue([
        1, '1', EcommerceEvents::AddToCart,
    ]);
    expect($x)->toBeArray();
    expect($x)->toEqual(['1', '1', EcommerceEvents::AddToCart->value]);
});
