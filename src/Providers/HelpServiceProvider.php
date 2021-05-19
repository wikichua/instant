<?php

namespace Wikichua\Instant\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class HelpServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->app->singleton('help', function ($app) {
            return new \Wikichua\Instant\Help();
        });
        // $this->app->booting(function () {
        $loader = AliasLoader::getInstance();
        $loader->alias('Help', \Wikichua\Instant\Facades\Help::class);
        // });
    }

    public function provides()
    {
        return ['help'];
    }
}
