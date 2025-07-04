<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'open_exchange_rates' => [
        'api_key' => env('OPEN_EXCHANGE_RATES_API_KEY'),
        'api_url' => env('OPEN_EXCHANGE_RATES_API_URL', 'https://openexchangerates.org/api/latest.json'),
        'supported_currencies' => ['BYN', 'USD', 'EUR', 'RUB'],
        'base_currency' => env('OPEN_EXCHANGE_RATES_BASE_CURRENCY', 'USD'),
    ],

    'currency' => [
        'base' => env('CURRENCY_BASE', 'USD'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'email_notification' => [
        'default_from_email' => env('EMAIL_NOTIFICATION_FROM_EMAIL', 'noreply@example.com'),
        'email_log_directory' => env('EMAIL_NOTIFICATION_LOG_DIRECTORY', 'app/emails'),
        'email_file_prefix' => env('EMAIL_NOTIFICATION_FILE_PREFIX', 'email_'),
    ],

];
