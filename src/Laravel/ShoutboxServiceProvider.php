<?php

namespace Shoutbox\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;

class ShoutboxServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/shoutbox.php' => config_path('shoutbox.php'),
        ], 'config');

        // Register custom mail transport
        Mail::extend('shoutbox', function () {
            return new ShoutboxTransport(
                config('services.shoutbox.key')
            );
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/shoutbox.php',
            'shoutbox'
        );
    }
}
