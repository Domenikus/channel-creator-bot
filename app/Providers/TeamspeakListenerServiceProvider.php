<?php

namespace App\Providers;

use App\Services\ClientServiceInterface;
use App\Services\Listeners\ChannelListener;
use App\Services\Listeners\TimeoutListener;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TeamspeakListenerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    const TAG_NAME = 'teamspeak-listener';

    public function provides(): array
    {
        return [TimeoutListener::class, ChannelListener::class];
    }

    public function register(): void
    {
        $service = $this->app->make(ClientServiceInterface::class);
        if (! $service instanceof ClientServiceInterface) {
            return;
        }

        $this->app->bind(TimeoutListener::class, function () {
            return new TimeoutListener();
        });

        $this->app->bind(ChannelListener::class, function () use ($service) {
            return new ChannelListener($service);
        });

        $this->app->tag([TimeoutListener::class, ChannelListener::class], self::TAG_NAME);
    }
}
