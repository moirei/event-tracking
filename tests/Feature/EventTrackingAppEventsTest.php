<?php

use Faker\Factory;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Event;
use MOIREI\EventTracking\Channel\GoogleAnalytics;
use MOIREI\EventTracking\Channel\Mixpanel;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Facades\Events;

uses()->group('event-tracking', 'app-events');

beforeEach(function () {
    include_once __DIR__.'/../CreateUsersTable.php';
    (new \CreateUsersTable)->up();
    $this->faker = Factory::create();
});

it('should track app events', function () {
    /** @var \Mockery\LegacyMockInterface */
    $eventChannel1 = \Mockery::mock(GoogleAnalytics::class.'[track,initialize]');
    /** @var \Mockery\LegacyMockInterface */
    $eventChannel2 = \Mockery::mock(Mixpanel::class.'[track,initialize]');

    $eventChannel1->shouldReceive('track');
    $eventChannel1->shouldReceive('initialize');
    $eventChannel2->shouldReceive('track');
    $eventChannel2->shouldReceive('initialize');

    $this->instance(GoogleAnalytics::class, $eventChannel1);
    $this->instance(Mixpanel::class, $eventChannel2);
    app()->register(EventTrackingServiceProvider::class);

    Events::listen(Registered::class);

    User::unguard();
    $user = User::create([
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
    ]);

    Event::dispatch(new Registered($user));
});
