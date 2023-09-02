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
        $channelTopic = config('teamspeak.channel_topic');
        $channelDescription = config('teamspeak.channel_description');
        $parentChannel = config('teamspeak.parent_channel');
        $channelClientLimit = config('teamspeak.channel_client_limit');
        $channelAdminGroupId = config('teamspeak.channel_admin_group_id');
        $channelCodec = config('teamspeak.channel_codec');
        $channelCodecQuality = config('teamspeak.channel_codec_quality');
        $channelNeededJoinPower = config('teamspeak.channel_needed_join_power');
        $channelNeededSubscribePower = config('teamspeak.channel_needed_subscribe_power');
        $channelNeededDescriptionViewPower = config('teamspeak.channel_needed_description_view_power');
        $channelNeededModifyPower = config('teamspeak.channel_needed_modify_power');
        $channelNeededTalkPower = config('teamspeak.channel_needed_talk_power');
        $defaultChannel = config('teamspeak.default_channel');
        $channelNameLists = config('channel-names.lists');
        $channelListName = config('channel-names.default');
        $channelNameClientLists = config('channel-names.clients');

        if (
            (!is_string($channelTopic)) ||
            (!is_string($channelDescription)) ||
            (!is_numeric($parentChannel)) ||
            (!is_numeric($channelClientLimit) && !is_null($channelClientLimit)) ||
            (!is_numeric($channelAdminGroupId) && !is_null($channelAdminGroupId)) ||
            (!is_string($channelCodec)) ||
            (!is_numeric($channelCodecQuality) && !is_null($channelCodecQuality)) ||
            (!is_numeric($channelNeededJoinPower) && !is_null($channelNeededJoinPower)) ||
            (!is_numeric($channelNeededSubscribePower) && !is_null($channelNeededSubscribePower)) ||
            (!is_numeric($channelNeededDescriptionViewPower) && !is_null($channelNeededDescriptionViewPower)) ||
            (!is_numeric($channelNeededModifyPower) && !is_null($channelNeededModifyPower)) ||
            (!is_numeric($channelNeededTalkPower) && !is_null($channelNeededTalkPower)) ||
            (!is_numeric($defaultChannel) && !is_null($defaultChannel)) ||
            (!is_array($channelNameLists)) ||
            (!is_array($channelNameClientLists)) ||
            (!is_string($channelListName)) ||
            (empty($channelNameLists[$channelListName]))
        ) {
            Log::error('Invalid environment variables');
            exit();
        }

        $channelNames = $channelNameLists[$channelListName];

        /**
         * @var int $parentChannel
         * @var string|null $channelTopic
         * @var string|null $channelDescription
         * @var int|null $channelClientLimit
         * @var int|null $channelAdminGroupId
         * @var string $channelCodec
         * @var int|null $channelCodecQuality
         * @var int|null $channelNeededJoinPower
         * @var int|null $channelNeededSubscribePower
         * @var int|null $channelNeededDescriptionViewPower
         * @var int|null $channelNeededModifyPower
         * @var int|null $channelNeededTalkPower
         * @var int|null $defaultChannel
         * */
        $this->app->bind(ClientServiceInterface::class, function () use (
            $channelCodecQuality,
            $channelCodec,
            $channelTopic,
            $channelDescription,
            $channelNeededModifyPower,
            $channelNeededDescriptionViewPower,
            $channelNeededTalkPower,
            $channelNameClientLists,
            $defaultChannel,
            $channelNeededSubscribePower,
            $channelNeededJoinPower,
            $channelClientLimit,
            $channelAdminGroupId,
            $parentChannel,
            $channelNames
        ) {
            return new ClientService(
                $parentChannel,
                $channelNames,
                channelCodec: $channelCodec,
                defaultChannel: $defaultChannel,
                channelTopic: $channelTopic,
                channelDescription: $channelDescription,
                channelClientLimit: $channelClientLimit,
                channelCodecQuality: $channelCodecQuality,
                channelAdminGroupId: $channelAdminGroupId,
                channelNeededJoinPower: $channelNeededJoinPower,
                channelNeededSubscribePower: $channelNeededSubscribePower,
                channelNeededDescriptionViewPower: $channelNeededDescriptionViewPower,
                channelNeededModifyPowerPower: $channelNeededModifyPower,
                channelNeededTalkPowerPower: $channelNeededTalkPower,
                channelNameClientLists: $channelNameClientLists
            );
        });
    }
}
