<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use MOIREI\EventTracking\Contracts\TrackableModel;
use MOIREI\EventTracking\EventTracking;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Observers\ModelObserver;

uses()->group('model-observer');

it('should make a new observer class', function () {
    $class = ModelObserver::make([]);

    expect($class)->toBeString();
    expect($class)->not->toEqual(ModelObserver::class);
    expect(is_subclass_of($class, ModelObserver::class))->toBeTrue();
    expect(class_exists($class))->toBeTrue();
});

it('should make a new observer instance', function () {
    $observer = ModelObserver::factory([]);

    expect($observer)->toBeObject();
    expect(is_subclass_of($observer, ModelObserver::class))->toBeTrue();
});

it('expects observer event to use class path', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $user = new User;
    $observer = ModelObserver::factory([]);

    $eventName = Helpers::resolveModelEvent(User::class, 'created');
    $eventProperties = invade($observer)->getEventProperties('created', $user, $eventName);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer event to use options name', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $user = new User;
    $eventName = 'User: Created';
    $observer = ModelObserver::factory([
        'created' => $eventName,
    ]);

    $eventProperties = invade($observer)->getEventProperties('created', $user, $eventName);
    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer event to use options name [2]', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $user = new User;
    $eventName = 'User: Created';
    $observer = ModelObserver::factory([
        'created' => [
            'name' => $eventName,
        ],
    ]);

    $eventProperties = invade($observer)->getEventProperties('created', $user, $eventName);
    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer event to use name from TrackableModel', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

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
    $observer = ModelObserver::factory([]);

    $eventProperties = invade($observer)->getEventProperties('created', $user, $eventName);

    $eventTracking->shouldReceive('track')->withArgs([$eventName, $eventProperties]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->created($user);
});

it('expects observer to use event property from options property', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $user = new class extends User
    {
        public $customProperties = [
            'a' => 1,
            'b' => 2,
        ];
    };
    $eventName = 'User: Created';
    $observer = ModelObserver::factory([
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
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

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
    $observer = ModelObserver::factory([
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
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $user = new User;
    $eventName = 'User: Created';
    $eventProperties = [
        'a' => 1,
        'b' => 2,
    ];
    $observer = ModelObserver::factory([
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

it('should not handle event not in default', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $observer = ModelObserver::factory([]);

    $eventTracking->shouldNotReceive('track');

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->forceDeleted(new User());
});

it('should handle event not in default with $all option', function () {
    /** @var object */
    $eventTracking = \Mockery::mock(EventTracking::class.'[track]', [new Request()]);

    $user = new User();
    $observer = ModelObserver::factory([
        '$all' => true,
    ]);

    $eventTracking->shouldReceive('track')->withArgs([Helpers::resolveModelEvent($user, 'forceDeleted'), []]);

    $this->instance(EventTracking::class, $eventTracking);
    app()->register(EventTrackingServiceProvider::class);

    $observer->forceDeleted($user);
});

it('should include only provided event with $only option', function () {
    $observer = ModelObserver::factory([
        '$only' => ['retrieved'],
    ]);
    expect(invade($observer)->handle)->toEqual(['retrieved']);
});

it('should exclude provided event with $except option', function () {
    $observer = ModelObserver::factory([
        '$except' => ['created'],
    ]);

    expect(in_array('created', invade($observer)->handle))->toBeFalse();
    expect(count(invade($observer)->handle))->toBeGreaterThan(count(invade($observer)->other));
});

it('should exclude additional events', function () {
    $observer = ModelObserver::factory([
        'retrieved' => 'Retrieved',
    ]);

    expect(in_array('retrieved', invade($observer)->handle))->toBeTrue();
    expect(count(invade($observer)->handle))->toBeGreaterThanOrEqual(2);
});
