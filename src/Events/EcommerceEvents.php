<?php

namespace MOIREI\EventTracking\Events;

enum EcommerceEvents: string
{
    case AddToCart = 'Add To Cart';
    case InitiateCheckout = 'Initiate Checkout';
    case Purchase = 'Purchase';
    case ProgressCheckout = 'Progress Checkout';
    case Search = 'Search';
    case ViewContent = 'View Content';
    case RemoveFromCart = 'Remove From Cart';
}
