<?php

namespace App\Providers;

use App\Services\Listeners\TimeoutListener;
use App\Services\UserServiceInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TeamspeakListenerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    const TAG_NAME = 'teamspeak-listener';

    public function boot(): void
    {
    }

    public function provides(): array
    {
        return [TimeoutListener::class];
    }

    public function register(): void
    {
        $service = $this->app->make(UserServiceInterface::class);
        if (! $service instanceof UserServiceInterface) {
            return;
        }

        $this->app->bind(TimeoutListener::class, function () {
            return new TimeoutListener();
        });

        $this->app->tag([TimeoutListener::class], self::TAG_NAME);
    }
}
