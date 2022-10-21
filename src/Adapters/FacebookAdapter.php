<?php

namespace MOIREI\EventTracking\Adapters;

use Illuminate\Support\Arr;
use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

/**
 * Basic example adapter for mapping Facebook Pixel
 * compatible data
 */
class FacebookAdapter extends EventAdapter
{
    /**
     * {@inheritdoc}
     */
    protected array $channels = ['facebook'];

    /**
     * {@inheritdoc}
     */
    public function only()
    {
        return [
            EcommerceEvents::AddToCart,
            EcommerceEvents::InitiateCheckout,
            EcommerceEvents::Purchase,
            EcommerceEvents::ProgressCheckout,
            EcommerceEvents::Search,
            EcommerceEvents::ViewContent,
            \Illuminate\Auth\Events\Registered::class,
        ];
    }

    /**
     * Map event properties.
     *
     * @param  array<string, \Closure>  $map
     */
    public function configure()
    {
        static::mapEvent(EcommerceEvents::AddToCart, 'AddToCart');
        static::mapEvent(EcommerceEvents::InitiateCheckout, 'InitiateCheckout');
        static::mapEvent(EcommerceEvents::ProgressCheckout, 'ProgressCheckout');
        static::mapEvent(EcommerceEvents::ViewContent, 'ViewContent');
        // static::mapEvent(EcommerceEvents::ItemClick, 'SelectContent');
        // static::mapEvent(Registered::class, 'CompleteRegistration');

        static::mapEventProperty(EcommerceEvents::AddToCart, function (EventPayload $payload) {
            return static::getActionData($payload->properties);
        });
        static::mapEventProperty(EcommerceEvents::InitiateCheckout, function (EventPayload $payload) {
            $data = static::getActionData($payload->properties);

            return array_merge($data, [
                'num_items' => count($data['content_ids']),
            ]);
        });
        static::mapEventProperty(EcommerceEvents::Search, function (EventPayload $payload) {
            $data = static::getActionData($payload->properties);

            return array_merge($data, [
                'search_string' => Helpers::getAny(
                    $payload->properties,
                    ['search_string', 'searchString', 'search_term', 'searchTerm', 'term']
                ),
            ]);
        });
        static::mapEventProperty(Registered::class, function (EventPayload $payload) {
            return [
                'content_name' => Helpers::get($payload->properties, 'name'),
                'currency' => Helpers::getAny($payload->properties, ['currency_code', 'currencyCode', 'currency']),
                'status' => Helpers::get($payload->properties, 'status'),
                'value' => Helpers::getAny($payload->properties, ['total', 'price', 'value']),
                'predicted_ltv' => Helpers::getAny($payload->properties, ['predicted_ltv', 'ltv']),
            ];
        });
    }

    protected static function getActionData($properties)
    {
        $contents = Helpers::getAny($properties, ['items', 'contents'], []);

        return [
            'content_ids' => array_filter(array_map(fn ($content) => Arr::get($content, 'id'), $contents)),
            'content_name' => Helpers::get($properties, 'name'),
            'content_type' => Helpers::getAny($properties, ['type', 'content_type', 'contentType'], 'product'),
            'content_category' => Helpers::get($properties, 'category'),
            'contents' => $contents,
            'currency' => Helpers::getAny($properties, ['currency_code', 'currencyCode', 'currency']),
            'value' => Helpers::getAny($properties, ['total', 'price', 'value']),
        ];
    }
}
