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
        $parentChannel = config('teamspeak.parent_channel');
        if (! is_numeric($parentChannel)) {
            throw new Exception('No or invalid parent channels');
        }

        $channelTemplate = config('teamspeak.channel_template');
        if (! is_string($channelTemplate)) {
            throw new Exception('Invalid channel template');
        }

        $channelClientLimit = config('teamspeak.channel_client_limit');
        if (! is_numeric($channelClientLimit) && ! is_null($channelClientLimit)) {
            throw new Exception('Invalid channel client limit');
        }

        $channelAdminGroupId = config('teamspeak.channel_admin_group_id');
        if (! is_numeric($channelAdminGroupId) && ! is_null($channelAdminGroupId)) {
            throw new Exception('Invalid channel admin group id');
        }

        $channelNeededJoinPower = config('teamspeak.channel_needed_join_power');
        if (! is_numeric($channelNeededJoinPower) && ! is_null($channelNeededJoinPower)) {
            throw new Exception('Invalid channel needed join power');
        }

        $channelNeededSubscribePower = config('teamspeak.channel_needed_subscribe_power');
        if (! is_numeric($channelNeededSubscribePower) && ! is_null($channelNeededSubscribePower)) {
            throw new Exception('Invalid channel needed subscribe power');
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
