<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use TeamSpeak3;
use TeamSpeak3_Node_Abstract;

class TeamspeakServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
    }

    public function provides(): array
    {
        return [TeamSpeak3::class];
    }

    public function register(): void
    {
        $this->app->singleton(TeamSpeak3::class, function ($app): TeamSpeak3_Node_Abstract {
            $uri = 'serverquery://'
                .config('teamspeak.query_user').':'
                .config('teamspeak.query_password').'@'
                .config('teamspeak.ip').':'
                .config('teamspeak.query_port').'/?server_port='
                .config('teamspeak.port').'&blocking=0&nickname='
                .config('teamspeak.bot_name');

            return TeamSpeak3::factory($uri);
        });
    }
}
