<?php

namespace App\Providers;

use App\Services\ClientService;
use App\Services\ClientServiceInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [ClientServiceInterface::class];
    }

    public function register(): void
    {
        $parentChannel = config('teamspeak.parent_channel');
        $channelTemplate = config('teamspeak.channel_template');
        $channelClientLimit = config('teamspeak.channel_client_limit');
        $channelAdminGroupId = config('teamspeak.channel_admin_group_id');
        $channelNeededJoinPower = config('teamspeak.channel_needed_join_power');
        $channelNeededSubscribePower = config('teamspeak.channel_needed_subscribe_power');

        if (
            (! is_numeric($parentChannel)) ||
            (! is_string($channelTemplate) ||
            (! is_numeric($channelClientLimit) && ! is_null($channelClientLimit)) ||
            (! is_numeric($channelAdminGroupId) && ! is_null($channelAdminGroupId)) ||
            (! is_numeric($channelNeededJoinPower) && ! is_null($channelNeededJoinPower)) ||
            (! is_numeric($channelNeededSubscribePower) && ! is_null($channelNeededSubscribePower))
            )
        ) {
            Log::error('Invalid environment variables');
            exit();
        }

        /** @var int|null $channelClientLimit */
        /** @var int|null $channelAdminGroupId */
        /** @var int|null $channelNeededJoinPower */
        /** @var int|null $channelNeededSubscribePower */
        $this->app->bind(ClientServiceInterface::class, function () use (
            $channelNeededSubscribePower,
            $channelNeededJoinPower, $channelClientLimit, $channelAdminGroupId, $channelTemplate, $parentChannel) {
            return new ClientService((int) $parentChannel, $channelTemplate, $channelClientLimit, $channelAdminGroupId, $channelNeededJoinPower, $channelNeededSubscribePower);
        });
    }
}
