<?php

namespace MOIREI\EventTracking\Channel;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Mixpanel as MixpanelAgent;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;

/**
 * Uses https://github.com/mixpanel/mixpanel-php
 */
class Mixpanel extends EventChannel
{
    protected MixpanelAgent $mixpanel;

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $configDefaults = [
            'consumer' => 'socket',
            'connect_timeout' => 2,
            'timeout' => 2,
        ];

        $this->mixpanel = MixpanelAgent::getInstance(
            Arr::get($config, 'token'),
            array_merge($configDefaults, $config)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function track(EventPayload $data)
    {
        $this->mixpanel->track(
            $data->event,
            array_filter($data->properties) + $this->getDeviceData($data)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function identify(IdentityPayload $data)
    {
        $this->mixpanel->people->set(
            $data->distinctId,
            $this->getIdentityPayload($data),
            $data->ip
        );
    }

    protected function getDeviceData(EventPayload $data): array
    {
        return array_filter([
            'Url' => $data->device->url,
            'Operating System' => $data->device->os,
            'Hardware' => $data->device->hardware,
            '$browser' => $data->device->browser,
            'Referrer' => $data->device->referer,
            '$referring_domain' => $data->device->refererDomain,
            'ip' => $data->device->ip,
        ]);
    }

    protected function getIdentityPayload(IdentityPayload $data): array
    {
        return array_filter([
            '$first_name' => $data->user->firstName,
            '$last_name' => $data->user->lastName,
            '$name' => $data->user->name,
            '$email' => $data->user->email,
            '$created' => ($data->user->createdAt
                ? (new Carbon())
                ->parse($data->user->createdAt)
                ->format('Y-m-d\Th:i:s')
                : null),
        ]);
    }
}
