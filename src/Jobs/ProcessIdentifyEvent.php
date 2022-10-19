<?php

namespace MOIREI\EventTracking\Jobs;

use MOIREI\EventTracking\EventHandler;
use MOIREI\EventTracking\Objects\IdentityPayload;

class ProcessIdentifyEvent extends EventJob
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        public IdentityPayload $data,
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
        EventHandler::identify($this->data, $this->channels, $this->adapters);
    }
}
