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

    // Token for source.fogos.pt (sent as the FPTS header by LegacyApi only).
    'fogos' => [
        'url'  => env('FOGOS_API_URL', 'https://source.fogos.pt'),
        'fpts' => env('FPTS', ''),
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
        // Service account JSON used to mint FCM OAuth tokens (topic subscribe).
        'service_account' => env('FIREBASE_SERVICE_ACCOUNT_PATH') ?: env('GOOGLE_APPLICATION_CREDENTIALS'),
    ],

    // Headless-Chrome sidecar that renders the per-fire OG card. The
    // controller renders the Blade to HTML in-process and POSTs it here;
    // the sidecar does setContent + screenshot, no navigation back into
    // this app.
    'og_renderer' => [
        'url' => env('OG_RENDERER_URL', 'http://og-renderer:3000'),
    ],

];
