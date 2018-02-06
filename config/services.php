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
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id' => '272247383214132',
        'client_secret' => '7302e725276b253061241efa2caadbad',
        'redirect' => 'https://fomo.watch/login/facebook/callback',
    ],

    'google' => [
        'client_id' => '109169933324-363ukaipc189uv7fdmbvvb329j1g0cbh.apps.googleusercontent.com',
        'client_secret' => '5_wGK0DEFU09elWJ-pOYCVk2',
        'redirect' => 'https://fomo.watch/login/google/callback',
    ],

    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT', 'AegJY451w10WvNb-nnwR56OCLVyS6g4lJ_E41aJECKdmmOSwhcUhYy8deBPrwqEFkEkQP5v3O4SucS-N'),
        'secret' => env('PAYPAL_SECRET', 'EIJOVXWCjbXoXHYtjXoWb5mffUEUb0xNYl9ezctZ-bCDMagHwZuTfTX_PZO1NL1k_n1oLDn8QEt94YMf'),
        'endpoint' => env('PAYPAL_ENDPOINT', 'https://apo.sandbox.paypal.com'),
    ]

];
