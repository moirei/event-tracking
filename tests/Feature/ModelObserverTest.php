<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use MOIREI\EventTracking\Contracts\TrackableModel;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Observers\ModelObserver;

uses()->group('model-observer');

it('expects observer event to use class path', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $user = new User;
    $observer = new ModelObserver([]);

    $eventName = Helpers::resolveModelEvent(User::class, 'created');
    $eventProperties = invade($observer)->getEventProperties($user, $eventName);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer event to use options name', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $user = new User;
    $eventName = 'User: Created';
    $observer = new ModelObserver([
        'created' => $eventName,
    ]);

    $eventProperties = invade($observer)->getEventProperties($user, $eventName);
    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer event to use options name [2]', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $user = new User;
    $eventName = 'User: Created';
    $observer = new ModelObserver([
        'created' => [
            'name' => $eventName,
        ],
    ]);

    $eventProperties = invade($observer)->getEventProperties($user, $eventName);
    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer event to use name from TrackableModel', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $eventName = 'User: Created';
    $user = new class($eventName) extends User implements TrackableModel
    {
        public function __construct(protected string $eventName)
        {
        }

        public function getEventName(string $event)
        {
            return $this->eventName;
        }

        public function getEventProperties(string $event)
        {
            return [];
        }
    };
    $observer = new ModelObserver([]);

    $eventProperties = invade($observer)->getEventProperties($user, $eventName);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer to use event property from options property', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $user = new class extends User
    {
        public $customProperties = [
            'a' => 1,
            'b' => 2,
        ];
    };
    $eventName = 'User: Created';
    $observer = new ModelObserver([
        'created' => [
            'name' => $eventName,
            'properties' => 'customProperties',
        ],
    ]);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $user->customProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer to use event property from options method', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $user = new class extends User
    {
        public function customProperties()
        {
            return [
                'a' => 1,
                'b' => 2,
            ];
        }
    };
    $eventName = 'User: Created';
    $observer = new ModelObserver([
        'created' => [
            'name' => $eventName,
            'properties' => 'customProperties',
        ],
    ]);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $user->customProperties()]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer to use event property from options array', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class . '[track]', [new Request()]);

    $user = new User;
    $eventName = 'User: Created';
    $eventProperties = [
        'a' => 1,
        'b' => 2,
    ];
    $observer = new ModelObserver([
        'created' => [
            'name' => $eventName,
            'properties' => $eventProperties,
        ],
    ]);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});
