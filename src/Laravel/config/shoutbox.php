<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shoutbox API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Shoutbox settings. The API key can be
    | found in your Shoutbox dashboard.
    |
    */

    'key' => env('SHOUTBOX_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Mail Settings
    |--------------------------------------------------------------------------
    |
    | You can configure default settings for all emails sent through Shoutbox.
    |
    */

    'from' => [
        'address' => env('SHOUTBOX_FROM'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    'reply_to' => env('SHOUTBOX_REPLY_TO'),
];
