<?php

use MOIREI\EventTracking\EventTrackingServiceProvider;

beforeEach(function () {
    app()->register(EventTrackingServiceProvider::class);
});

xit('should auto track listed events', function () {
    //
});

xit('should not auto track listed events when disabled', function () {
    //
});

xit('should handle tracked event of instance type TrackableEvent', function () {
    //
});

xit('should not handle tracked event that is not of instance type TrackableEvent', function () {
    //
});
