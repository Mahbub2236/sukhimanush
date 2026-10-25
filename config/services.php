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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms' => [
        'driver' => env('SMS_GATEWAY', 'log'),
        'api_key' => env('SMS_API_KEY', ''),
        'sender_id' => env('SMS_SENDER_ID', 'SukhiManush'),
        'username' => env('SMS_USERNAME', ''),
        'password' => env('SMS_PASSWORD', ''),
        'custom_url' => env('SMS_API_URL', ''),
    ],

    'bkash' => [
        'app_key' => env('BKASH_APP_KEY', ''),
        'app_secret' => env('BKASH_APP_SECRET', ''),
        'username' => env('BKASH_USERNAME', ''),
        'password' => env('BKASH_PASSWORD', ''),
        'webhook_secret' => env('BKASH_WEBHOOK_SECRET', ''),
        'secret' => env('BKASH_WEBHOOK_SECRET', ''),
        'sandbox' => env('BKASH_SANDBOX', true),
    ],

    'nagad' => [
        'merchant_id' => env('NAGAD_MERCHANT_ID', ''),
        'public_key' => env('NAGAD_PG_PUBLIC_KEY', ''),
        'private_key' => env('NAGAD_MERCHANT_PRIVATE_KEY', ''),
        'webhook_secret' => env('NAGAD_WEBHOOK_SECRET', ''),
        'secret' => env('NAGAD_WEBHOOK_SECRET', ''),
        'sandbox' => env('NAGAD_SANDBOX', true),
    ],

    'sslcommerz' => [
        'store_id' => env('SSLCZ_STORE_ID', ''),
        'store_passwd' => env('SSLCZ_STORE_PASSWORD', ''),
        'webhook_secret' => env('SSLCZ_WEBHOOK_SECRET', ''),
        'secret' => env('SSLCZ_WEBHOOK_SECRET', ''),
        'sandbox' => env('SSLCZ_SANDBOX', true),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
    ],

];
