<?php

namespace MOIREI\EventTracking;

use Illuminate\Support\ServiceProvider;
use MOIREI\EventTracking\Contracts\TrackableEvent;
use MOIREI\EventTracking\Facades\Events;

class EventTrackingServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/event-tracking.php' => config_path('event-tracking.php'),
            ], 'event-tracking');
        }

        if (! config('event-tracking.auto_tracking.disabled', false)) {
            Events::observe(config('event-tracking.auto_tracking.observe', []));
            Events::listen(TrackableEvent::class);
            Events::listen(config('event-tracking.auto_tracking.listen', []));
        }

        Events::registerAdapter(config('event-tracking.adapters', []));
        Events::registerChannel(config('event-tracking.channels', []));
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/event-tracking.php', 'event-tracking');
        $this->app->singleton('eventTracking', EventTracking::class);
    }

    public function provides(): array
    {
        return ['eventTracking'];
    }
}
