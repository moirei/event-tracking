<?php

namespace MOIREI\EventTracking\Channels;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use PostHog\PostHog as PosthogAgent;
use MOIREI\EventTracking\Objects\EventPayload;
use MOIREI\EventTracking\Objects\IdentityPayload;

/**
 * Uses https://github.com/posthog/posthog-php
 */
class Posthog extends EventChannel
{
    /**
     * Flush the queue when destructing
     */
    public function __destruct()
    {
        PosthogAgent::flush();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array $config)
    {
        $configDefaults = [
            'consumer' => 'lib_curl',
            'timeout' => 10,
        ];

        PosthogAgent::init(
            Arr::get($config, 'token'),
            array_merge($configDefaults, $config, ['host' => Arr::get($config, 'host')])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function track(EventPayload $data)
    {
        PosthogAgent::capture([
            'distinctId' => $data->user?->id ?: sha1(session()->getId()),
            'event' => $data->event,
            'properties' => array_filter($data->properties) + $this->getDeviceData($data),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function identify(IdentityPayload $data)
    {
        PosthogAgent::identify([
            'distinctId' => $data->user->id,
            'properties' => array_filter($data->properties) + $this->getIdentityPayload($data),
        ]);
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
            'first_name' => $data->user->firstName,
            'last_name' => $data->user->lastName,
            'name' => $data->user->name,
            'email' => $data->user->email,
            'created' => ($data->user->createdAt
                ? (new Carbon())
                ->parse($data->user->createdAt)
                ->format('Y-m-d\Th:i:s')
                : null),
            'last_seen' => ($data->user->updatedAt
                ? (new Carbon())
                ->parse($data->user->updatedAt)
                ->format('Y-m-d\Th:i:s')
                : null),
            'city' => $data->user->city,
            'region' => $data->user->region,
            'country' => $data->user->country,
        ]);
    }
}
