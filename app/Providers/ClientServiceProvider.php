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
        $channelClientLimit = config('teamspeak.channel_client_limit');
        $channelAdminGroupId = config('teamspeak.channel_admin_group_id');
        $channelNeededJoinPower = config('teamspeak.channel_needed_join_power');
        $channelNeededSubscribePower = config('teamspeak.channel_needed_subscribe_power');
        $defaultChannel = config('teamspeak.default_channel');
        $channelNameLists = config('channel-names.lists');
        $channelListName = config('channel-names.default');
        $channelNameClientLists = config('channel-names.clients');

        if (
            (! is_numeric($parentChannel)) ||
            (! is_numeric($channelClientLimit) && ! is_null($channelClientLimit)) ||
            (! is_numeric($channelAdminGroupId) && ! is_null($channelAdminGroupId)) ||
            (! is_numeric($channelNeededJoinPower) && ! is_null($channelNeededJoinPower)) ||
            (! is_numeric($channelNeededSubscribePower) && ! is_null($channelNeededSubscribePower)) ||
            (! is_numeric($defaultChannel) && ! is_null($defaultChannel)) ||
            (! is_array($channelNameLists)) ||
            (! is_array($channelNameClientLists)) ||
            (! is_string($channelListName)) ||
            (empty($channelNameLists[$channelListName]))
        ) {
            Log::error('Invalid environment variables');
            exit();
        }

        $channelNames = $channelNameLists[$channelListName];

        /** @var int|null $channelClientLimit */
        /** @var int|null $channelAdminGroupId */
        /** @var int|null $channelNeededJoinPower */
        /** @var int|null $channelNeededSubscribePower */
        /** @var int|null $defaultChannel */
        $this->app->bind(ClientServiceInterface::class, function () use (
            $channelNameClientLists,
            $defaultChannel,
            $channelNeededSubscribePower,
            $channelNeededJoinPower, $channelClientLimit, $channelAdminGroupId, $parentChannel, $channelNames) {
            return new ClientService((int) $parentChannel, $channelNames, $defaultChannel, $channelClientLimit, $channelAdminGroupId, $channelNeededJoinPower, $channelNeededSubscribePower, $channelNameClientLists);
        });
    }
}
