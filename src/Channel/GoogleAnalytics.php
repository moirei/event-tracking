<?php

namespace MOIREI\EventTracking\Channel;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;
use TheIconic\Tracking\GoogleAnalytics\Analytics;

/**
 * Uses https://github.com/mixpanel/mixpanel-php
 */
class GoogleAnalytics extends EventChannel
{
    protected Analytics $analytics;

    protected string $measurementId;

    protected string $apiSecret;

    protected string $clientId;

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $this->analytics = new Analytics(Arr::get($config, 'ssl', false), Arr::get($config, 'disabled', false));

        $this->analytics
            ->setProtocolVersion(Arr::get($config, 'protocol_version', 1))
            ->setTrackingId(Arr::get($config, 'tracking_id'));

        if (Arr::get($config, 'anonymize_ip', false)) {
            $this->analytics->setAnonymizeIp('1');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function track(EventPayload $data)
    {
        $this->analytics->setEventAction($data->event)
            ->setEventValue(array_filter($data->properties))
            ->sendEvent();
    }

    /**
     * {@inheritdoc}
     */
    public function identify(IdentityPayload $data)
    {
        //
    }

    protected function getDeviceData(EventPayload $data): array
    {
        return array_filter([
            //
        ]);
    }

    protected function getIdentityPayload(IdentityPayload $data): array
    {
        return array_filter([
            //
        ]);
    }

    protected function postEvents(array $events)
    {
        $url = "https://www.google-analytics.com/mp/collect?measurement_id={$this->measurementId}&api_secret={$this->apiSecret}";

        Http::accept('application/json')->post($url, array_filter([
            'client_id' => $this->clientId,
            'user_id' => $this->userId,
            'events' => $events,
        ]));
    }
}
