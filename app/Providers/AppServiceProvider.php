<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());

        config([
            'logging.channels.daily.path' => \Phar::running()
                ? dirname(\Phar::running(false)).'/logs/channel-creator-bot.log'
                : storage_path('logs/channel-creator-bot.log'),
        ]);
    }
}
