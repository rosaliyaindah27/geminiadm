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

    'nowpayments' => [
        'api_key' => env('NOWPAYMENTS_API_KEY', 'A2BYB64-8DFMH2B-PESRT93-TYZK4GK'),
        'ipn_secret' => env('NOWPAYMENTS_IPN_SECRET', 'FPRQamdSK2vPtSHDUGbtcs5GHSCeuW4d'),
        'public_key' => env('NOWPAYMENTS_PUBLIC_KEY', '0d39caa5-6042-4284-a614-451f3958ec8b'),
        'base_url' => env('NOWPAYMENTS_BASE_URL', 'https://api.nowpayments.io/v1'),
        'sandbox' => env('NOWPAYMENTS_SANDBOX', false),
    ],

];
