<?php

use Illuminate\Auth\Events\Login;
use MOIREI\EventTracking\EventTracking;

uses()->group('event-tracking-listen');

it('should listen to event with map name using array', function () {
    EventTracking::listen([
        Login::class => 'LoginEvent',
    ]);
    expect(EventTracking::getEventMaps())->toHaveKey(Login::class);
    expect(EventTracking::getEventMaps()[Login::class])->toHaveKey('name');
    expect(EventTracking::getEventMaps()[Login::class]['name'])->toEqual('LoginEvent');
});

it('should listen to event with map name and property using array', function () {
    EventTracking::listen([
        Login::class => [
            'name' => 'LoginEvent',
            'properties' => 'toArray',
        ],
    ]);
    expect(EventTracking::getEventMaps())->toHaveKey(Login::class);
    expect(EventTracking::getEventMaps()[Login::class])->toHaveKey('name');
    expect(EventTracking::getEventMaps()[Login::class])->toHaveKey('properties');
    expect(EventTracking::getEventMaps()[Login::class]['name'])->toEqual('LoginEvent');
    expect(EventTracking::getEventMaps()[Login::class]['properties'])->toEqual('toArray');
});
