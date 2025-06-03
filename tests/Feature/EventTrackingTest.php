<?php

use MOIREI\EventTracking\Channels\GoogleAnalytics;
use MOIREI\EventTracking\Channels\Mixpanel;
use MOIREI\EventTracking\Channels\PostHog;
use MOIREI\EventTracking\EventTrackingServiceProvider;
use MOIREI\EventTracking\Facades\Events;

uses()->group('event-tracking');

it('should send event to all channels', function () {
    $eventChannel1 = \Mockery::mock(GoogleAnalytics::class.'[track,initialize]');
    $eventChannel2 = \Mockery::mock(Mixpanel::class.'[track,initialize]');
    $eventChannel3 = \Mockery::mock(PostHog::class.'[track,initialize]');

    $eventChannel1->shouldReceive('track');
    $eventChannel1->shouldReceive('initialize');
    $eventChannel2->shouldReceive('track');
    $eventChannel2->shouldReceive('initialize');
    $eventChannel3->shouldReceive('track');
    $eventChannel3->shouldReceive('initialize');

    $this->instance(GoogleAnalytics::class, $eventChannel1);
    $this->instance(Mixpanel::class, $eventChannel2);
    $this->instance(PostHog::class, $eventChannel3);
    app()->register(EventTrackingServiceProvider::class);

    Events::track('my-event');
});

it('should send event to GA channel', function () {
    $eventChannel1 = \Mockery::mock(GoogleAnalytics::class.'[track,initialize]');
    $eventChannel2 = \Mockery::mock(Mixpanel::class.'[track,initialize]');
    $eventChannel3 = \Mockery::mock(PostHog::class.'[track,initialize]');

    $eventChannel1->shouldReceive('track')->times(2);
    $eventChannel1->shouldReceive('initialize');
    $eventChannel2->shouldNotReceive('initialize');
    $eventChannel2->shouldNotReceive('track');
    $eventChannel3->shouldNotReceive('initialize');
    $eventChannel3->shouldNotReceive('track');

    $this->instance(GoogleAnalytics::class, $eventChannel1);
    $this->instance(Mixpanel::class, $eventChannel2);
    $this->instance(PostHog::class, $eventChannel3);
    app()->register(EventTrackingServiceProvider::class);

    Events::only('ga')->track('my-event');
    Events::except('mixpanel')->track('my-event');
});
