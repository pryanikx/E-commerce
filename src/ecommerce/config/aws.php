<?php

return [
    'credentials' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
    ],
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'version' => 'latest',

    // LocalStack endpoint
    'endpoint' => env('AWS_ENDPOINT_URL'),
    'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),

    'Ses' => [
        'region' => env('SES_REGION', env('AWS_DEFAULT_REGION', 'us-east-1')),
        'version' => 'latest',
        'credentials' => [
            'key' => env('SES_KEY', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('SES_SECRET', env('AWS_SECRET_ACCESS_KEY')),
        ],
        'endpoint' => env('SES_ENDPOINT', env('AWS_ENDPOINT_URL')),
    ],

    'S3' => [
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'version' => 'latest',
        'bucket' => env('AWS_BUCKET'),
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
        'endpoint' => env('AWS_ENDPOINT_URL'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],
];
