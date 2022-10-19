<?php

use MOIREI\EventTracking\Adapters\EventAdapter;
use MOIREI\EventTracking\Helpers;

uses()->group('helpers', 'resolve-adapters');

it('should resolve adapters', function () {
    $dapters = Helpers::resolveAdapters([
        \MOIREI\EventTracking\Adapters\GaAdapter::class,
    ]);
    expect($dapters)->toHaveCount(1);
    expect($dapters[0])->toBeInstanceOf(EventAdapter::class);
});

it('should cache resolved adapters', function () {
    Helpers::resolveAdapters([
        \MOIREI\EventTracking\Adapters\GaAdapter::class,
        \MOIREI\EventTracking\Adapters\FacebookAdapter::class,
    ]);
    expect(Helpers::$adaptersCache)->toHaveKey(\MOIREI\EventTracking\Adapters\FacebookAdapter::class);
    expect(Helpers::$adaptersCache[\MOIREI\EventTracking\Adapters\FacebookAdapter::class])->toBeInstanceOf(EventAdapter::class);
});
