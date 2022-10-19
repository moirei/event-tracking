<?php

namespace MOIREI\EventTracking\Jobs;

use MOIREI\EventTracking\EventHandler;
use MOIREI\EventTracking\Objects\EventPayload;

class ProcessTrackEvent extends EventJob
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        public EventPayload $eventPayload,
        public array $channels,
        public array $adapters,
    ) {
        parent::__construct();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        EventHandler::event($this->eventPayload, $this->channels, $this->adapters);
    }
}
