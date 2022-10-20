<?php

use Illuminate\Http\Request;
use MOIREI\EventTracking\EventTracking;

uses()->group('super-properties');

beforeEach(function () {
    $request = new Request();
    $this->eventTracking = new EventTracking($request);
});

it('should set super properties', function () {
    $properties = [
        'a' => 1,
        'b' => 2,
    ];

    expect($this->eventTracking->getSuperProperties())->toBeEmpty();

    $this->eventTracking->superProperties($properties);

    expect($this->eventTracking->getSuperProperties())->not->toBeEmpty();
    expect($this->eventTracking->getSuperProperties())->toEqual($properties);
});

it('should not override existing properties when setting super properties', function () {
    $properties1 = ['a' => 1];
    $properties2 = ['b' => 2];

    $this->eventTracking->superProperties($properties1);

    expect($this->eventTracking->getSuperProperties())->toEqual($properties1);

    $this->eventTracking->superProperties($properties2);

    expect($this->eventTracking->getSuperProperties())->not->toEqual($properties1);

    expect($this->eventTracking->getSuperProperties())->toHaveKeys(array_keys($properties1));
    expect($this->eventTracking->getSuperProperties())->toHaveKeys(array_keys($properties2));
});
