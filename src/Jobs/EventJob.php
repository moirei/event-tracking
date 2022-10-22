<?php

namespace MOIREI\EventTracking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use MOIREI\EventTracking\Facades\Events;

abstract class EventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public array $channels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('event-tracking.queue.disabled')) {
            $this->onConnection('sync');
        } else {
            $this->onConnection(config('event-tracking.queue.connection', 'sync'));
        }
        $this->onQueue(config('event-tracking.queue.queue'));
        $this->through(config('event-tracking.queue.middleware', []));
    }

    /**
     * Handle event completed.
     *
     * @return void
     */
    public function destroy()
    {
        // destroy all channel instances created by this job
        foreach ($this->channels as $channel) {
            $instance = Events::getChannel($channel);
            if ($instance) {
                unset($instance);
            }
        }
    }
}
