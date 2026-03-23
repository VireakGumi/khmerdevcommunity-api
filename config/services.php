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

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI', '/oauth/github/callback'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/oauth/google/callback'),
    ],

    'frontend' => [
        'url' => env('FRONTEND_URL', 'http://localhost:9000'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'service_account_json' => env('FIREBASE_SERVICE_ACCOUNT_JSON'),
        'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH'),
        'web_api_key' => env('FIREBASE_WEB_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),
        'web_vapid_key' => env('FIREBASE_WEB_VAPID_KEY'),
        'icon' => env('FIREBASE_NOTIFICATION_ICON', env('FRONTEND_URL', 'http://localhost:9000').'/icons/icon-192x192.png'),
        'badge' => env('FIREBASE_NOTIFICATION_BADGE', env('FRONTEND_URL', 'http://localhost:9000').'/icons/icon-128x128.png'),
    ],

];
