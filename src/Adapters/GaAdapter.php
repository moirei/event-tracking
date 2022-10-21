<?php

namespace MOIREI\EventTracking\Adapters;

use Illuminate\Support\Arr;
use MOIREI\EventTracking\Events\EcommerceEvents;
use MOIREI\EventTracking\Helpers;
use MOIREI\EventTracking\Objects\EventPayload;

/**
 * Basic example adapter for mapping Google Analytics
 * compatible data
 */
class GaAdapter extends EventAdapter
{
    /**
     * {@inheritdoc}
     */
    protected array $channels = ['ga'];

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
            EcommerceEvents::RemoveFromCart,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function eventPropertiesAs(EventPayload $payload): array
    {
        return static::getActionData($payload->properties);
    }

    /**
     * Map event properties.
     *
     * @param  array<string, \Closure>  $map
     */
    public function configure()
    {
        static::mapEvent(EcommerceEvents::AddToCart, 'add_to_cart');
        static::mapEvent(EcommerceEvents::InitiateCheckout, 'begin_checkout');
        static::mapEvent(EcommerceEvents::Purchase, 'purchase');
        static::mapEvent(EcommerceEvents::ProgressCheckout, 'checkout_progress');
        static::mapEvent(EcommerceEvents::ViewContent, 'view_item');
        static::mapEvent(EcommerceEvents::Search, 'search');
        // static::mapEvent('ItemClick', 'select_content');
        static::mapEvent(EcommerceEvents::RemoveFromCart, 'remove_from_cart');
    }

    protected static function getActionData($properties)
    {
        return [
            'id' => Helpers::getAny($properties, ['id', 'transaction_id', 'transactionId']),
            'affiliation' => Helpers::get($properties, 'affiliation'),
            'value' => Helpers::getAny($properties, ['total', 'price', 'value']),
            'currency' => Helpers::getAny($properties, ['currency_code', 'currencyCode', 'currency']),
            'tax' => Helpers::get($properties, 'tax'),
            'shipping' => Helpers::get($properties, 'shipping'),
            'checkout_step' => Helpers::getAny($properties, ['checkout_step', 'checkoutStep']),
            'checkout_option' => Helpers::getAny($properties, ['checkout_option', 'checkoutOption']),
            'items' => array_map(function ($item) {
                return static::getProductProperties($item);
            }, Helpers::get($properties, 'items', [])),
        ];
    }

    protected static function getProductProperties($properties)
    {
        return Arr::only(
            $properties,
            ['id', 'name', 'brand', 'category', 'variant', 'quantity', 'price']
        );
    }
}
