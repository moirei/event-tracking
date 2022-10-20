<?php

namespace MOIREI\EventTracking\Channels;

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
     * Flush the queue when destructing
     */
    public function __destruct()
    {
        $this->mixpanel->flush();
        $this->mixpanel->people->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $configDefaults = [
            'consumer' => 'socket',
            'connect_timeout' => 10,
            'timeout' => 10,
        ];

        $this->mixpanel = MixpanelAgent::getInstance(
            Arr::get($config, 'token'),
            array_merge($configDefaults, $config, [
                'error_callback' => function ($code, $msg) {
                    error_log('error: ' . $msg);
                    error_log('code: ' . $code);
                }
            ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function track(EventPayload $data)
    {
        $data->properties['distinct_id'] = $data->user?->id;
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
            $data->user->id,
            array_filter($data->properties) + $this->getIdentityPayload($data),
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
