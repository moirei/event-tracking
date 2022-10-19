<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Data channels
    |--------------------------------------------------------------------------
    |
    | Configure channels to send event data to.
    */
    'channels' => [
        /*
        |--------------------------------------------------------------------------
        | Google Analytics Channel
        |--------------------------------------------------------------------------
        |
        | Requires installing and configuring mixpanel/mixpanel-php
        | @see https://github.com/mixpanel/mixpanel-php
        */
        'mixpanel' => [
            'handler' => \MOIREI\EventTracking\Channel\Mixpanel::class,
            /*
            | Accepts array or string: use string to refer to another config.
            | Use configuration options for mixpanel/mixpanel-php
            | Be sure to include "token" key
            */
            'config' => 'services.mixpanel',
            'disabled' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | Google Analytics Channel
        |--------------------------------------------------------------------------
        |
        | Requires installing and configuring theiconic/php-ga-measurement-protocol
        | @see https://github.com/theiconic/php-ga-measurement-protocol
        */
        'ga' => [
            'handler' => \MOIREI\EventTracking\Channel\GoogleAnalytics::class,
            'config' => [
                /*
                |--------------------------------------------------------------------------
                | Google Analytics Tracking ID
                |--------------------------------------------------------------------------
                |
                | Your Google Analytics tracking ID / web property ID. The format is UA-XXXX-Y.
                | @see https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters#tid
                */
                'tracking_id' => env('GA_ID'),

                /*
                |--------------------------------------------------------------------------
                | Measurement Protocol Version
                |--------------------------------------------------------------------------
                |
                | The Protocol version. The current value is '1'.
                | This will only change when there are changes made that are not backwards compatible.
                | @see https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters#v
                */
                'protocol_version' => 1,

                /*
                |--------------------------------------------------------------------------
                | URL Endpoint - SSL Support: Send Data over SSL
                |--------------------------------------------------------------------------
                |
                | This option controls the URL endpoint of the Measurement Protocol.
                | To send data over SSL, set true.
                |
                | @see https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters#tid
                */
                'ssl' => false,

                /*
                |--------------------------------------------------------------------------
                | Disable Hits
                |--------------------------------------------------------------------------
                |
                | This option controls enabling or disabling the library.
                | Useful in Staging/Dev environments when you don't want to actually send hits to GA.
                | When disabled, it returns a AnalyticsResponseInterface object that returns empty values.
                */
                'disabled' => false,

                /*
                |--------------------------------------------------------------------------
                | Anonymize IP
                |--------------------------------------------------------------------------
                |
                | When set to True, the IP address of the sender will be anonymized.
                | @see https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters#aip
                */
                'anonymize_ip' => false,
            ],
            'disabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic Tracking
    |--------------------------------------------------------------------------
    |
    | Track model and app events automatically.
    */
    'auto_tracking' => [
        'disabled' => false,
        'observe' => [
            // \App\Models\User::class,
            \App\Models\User::class => [
                'created' => [
                    'name' => 'User: Registered',
                    'properties' => 'toArray',
                ],
                'deleted' => 'User: Deactivated',
            ],
        ],
        'listen' => [
            \Illuminate\Auth\Events\Login::class => 'Login',
            \Illuminate\Auth\Events\Logout::class => 'Logout',
            \Illuminate\Auth\Events\Registered::class,
            // \Illuminate\Auth\Events\Lockout::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Event adapters
    |--------------------------------------------------------------------------
    |
    | Configure adapters to intercept and modify event data.
    */
    'adapters' => [
        \MOIREI\EventTracking\Adapters\FacebookAdapter::class,
        \MOIREI\EventTracking\Adapters\GaAdapter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue options
    |--------------------------------------------------------------------------
    |
    | Track events are handled as a job in the configured queue.
    | Set `disabled` to `true` to always use `sync` connection.
    */
    'queue' => [
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue' => null,
        'middleware' => [],
        'disabled' => false,
    ],

];
