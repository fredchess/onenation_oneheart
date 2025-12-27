<?php

return [
    'stripe' => [
        'secret_key' => env('STRIPE_SECRET', ''),
        'publishable_key' => env('STRIPE_PUBLIC', ''),
    ],
];
