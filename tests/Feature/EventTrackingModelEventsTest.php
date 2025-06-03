<?php

use Faker\Factory;
use Illuminate\Foundation\Auth\User;
use MOIREI\EventTracking\Channels\GoogleAnalytics;
use MOIREI\EventTracking\Channels\Mixpanel;
use MOIREI\EventTracking\Channels\Posthog;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Facades\Events;

uses()->group('event-tracking', 'model-events');

beforeEach(function () {
    include_once __DIR__.'/../CreateUsersTable.php';
    (new \CreateUsersTable)->up();
    $this->faker = Factory::create();
});

it('should track model events', function () {
    $eventChannel1 = \Mockery::mock(GoogleAnalytics::class.'[track,initialize]');
    $eventChannel2 = \Mockery::mock(Mixpanel::class.'[track,initialize]');
    $eventChannel3 = \Mockery::mock(Posthog::class.'[track,initialize]');

    $eventChannel1->shouldReceive('track');
    $eventChannel1->shouldReceive('initialize');
    $eventChannel2->shouldReceive('track');
    $eventChannel2->shouldReceive('initialize');
    $eventChannel3->shouldReceive('track');
    $eventChannel3->shouldReceive('initialize');

    $this->instance(GoogleAnalytics::class, $eventChannel1);
    $this->instance(Mixpanel::class, $eventChannel2);
    $this->instance(Posthog::class, $eventChannel3);
    app()->register(EventTrackingServiceProvider::class);

    Events::observe(User::class);

    User::unguard();
    User::create([
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
    ]);
});
