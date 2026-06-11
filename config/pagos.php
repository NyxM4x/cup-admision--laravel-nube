<?php

return [
    'stripe' => [
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
     'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'base_url' => env('PAYPAL_MODE') === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com',
    ],
];