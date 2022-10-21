<?php

use MOIREI\EventTracking\Channels\GoogleAnalytics;
use MOIREI\EventTracking\Channels\Mixpanel;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Facades\Events;

uses()->group('event-tracking');

it('should send event to all channels', function () {
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

    Events::track('my-event');
});

it('should send event to GA channel', function () {
    /** @var \Mockery\LegacyMockInterface */
    $eventChannel1 = \Mockery::mock(GoogleAnalytics::class.'[track,initialize]');
    /** @var \Mockery\LegacyMockInterface */
    $eventChannel2 = \Mockery::mock(Mixpanel::class.'[track,initialize]');

    $eventChannel1->shouldReceive('track')->times(2);
    $eventChannel1->shouldReceive('initialize');
    $eventChannel2->shouldNotReceive('initialize');
    $eventChannel2->shouldNotReceive('track');

    $this->instance(GoogleAnalytics::class, $eventChannel1);
    $this->instance(Mixpanel::class, $eventChannel2);
    app()->register(EventTrackingServiceProvider::class);

    Events::only('ga')->track('my-event');
    Events::except('mixpanel')->track('my-event');
});
