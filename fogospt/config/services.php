<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'gaia' => [
        'base' => env('GAIA_API_BASE', 'https://wildfires.gaia-project.cloud/api'),
        'key'  => env('GAIA_API_KEY', ''),
    ],

    // Read via config('services.google_analytics') in views — calling env()
    // directly in a Blade template returns null once `config:cache` is run.
    'google_analytics' => env('GOOGLE_ANALYTICS', ''),

    'firebase' => [
        'app_id' => env('FIREBASE_APP_ID', ''),
        'vapid_key' => env('FIREBASE_VAPID_KEY', ''),
    ],

];
