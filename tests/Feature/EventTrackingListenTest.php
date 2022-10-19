<?php

use Illuminate\Auth\Events\Login;
use MOIREI\EventTracking\EventTracking;

uses()->group('event-tracking-listen');

it('should listen to event with map name using array', function () {
    EventTracking::listen([
        Login::class => 'LoginEvent',
    ]);
    expect(EventTracking::$globalEventMap)->toHaveKey(Login::class);
    expect(EventTracking::$globalEventMap[Login::class])->toHaveKey('name');
    expect(EventTracking::$globalEventMap[Login::class]['name'])->toEqual('LoginEvent');
});

it('should listen to event with map name and property using array', function () {
    EventTracking::listen([
        Login::class => [
            'name' => 'LoginEvent',
            'properties' => 'toArray',
        ],
    ]);
    expect(EventTracking::$globalEventMap)->toHaveKey(Login::class);
    expect(EventTracking::$globalEventMap[Login::class])->toHaveKey('name');
    expect(EventTracking::$globalEventMap[Login::class])->toHaveKey('properties');
    expect(EventTracking::$globalEventMap[Login::class]['name'])->toEqual('LoginEvent');
    expect(EventTracking::$globalEventMap[Login::class]['properties'])->toEqual('toArray');
});
