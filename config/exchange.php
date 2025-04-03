<?php

return [
    'endpoint' => env('EXCHANGE_RATE_ENDPOINT', 'https://open.er-api.com/v6/latest/'),
    'timeout' => env('EXCHANGE_RATE_REQUEST_TIMEOUT', 5),
    'cache' => [
        'key' => 'rate-exchange-from-?-to-?',
        'ttl' => env('EXCHANGE_RATE_CACHE_TTL', 60),
    ],
    'defaults' => [
        'USD' => [
            'EUR' => env('EXCHANGE_RATE_DEFAULT_USD_TO_EUR', 0.85),
        ],
    ],
];
