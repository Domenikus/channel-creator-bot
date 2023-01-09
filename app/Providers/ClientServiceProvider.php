<?php

namespace App\Providers;

use App\Services\ClientService;
use App\Services\ClientServiceInterface;
use Exception;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [ClientServiceInterface::class];
    }

    public function register(): void
    {
        $watchedChannels = is_string(config('teamspeak.watched_channels')) ? explode(',', config('teamspeak.watched_channels')) : [];
        foreach ($watchedChannels as $watchedChannel) {
            if (! is_numeric($watchedChannel)) {
                throw new Exception('No or invalid watched channels provided');
            }
        }

        $channelTemplate = config('teamspeak.channel_template');
        if (! is_string($channelTemplate)) {
            throw new Exception('Invalid channel template');
        }

        $channelAdmin = config('teamspeak.channel_admin');
        if (! is_bool($channelAdmin)) {
            throw new Exception('Invalid channel admin provided');
        }

        $this->app->bind(ClientServiceInterface::class, function () use ($channelAdmin, $channelTemplate, $watchedChannels) {
            return new ClientService($watchedChannels, $channelTemplate, $channelAdmin);
        });
    }
}
