<?php

namespace App\Services;

use App\Services\Gateways\TeamspeakGateway;

class ClientService implements ClientServiceInterface
{
    public function __construct(
        protected int $parentChannel,
        protected array $channelNames,
        protected string $channelCodec,
        protected ?int $defaultChannel,
        protected ?string $channelTopic = null,
        protected ?string $channelDescription = null,
        protected ?int $channelClientLimit = null,
        protected ?int $channelCodecQuality = null,
        protected ?int $channelAdminGroupId = null,
        protected ?int $channelNeededJoinPower = null,
        protected ?int $channelNeededSubscribePower = null,
        protected ?int $channelNeededDescriptionViewPower = null,
        protected ?int $channelNeededModifyPowerPower = null,
        protected ?int $channelNeededTalkPowerPower = null,
        protected array $channelNameClientLists = []
    ) {
    }

    public function handleClientMove(int $clientId, int $targetChannelId): void
    {
        if ($targetChannelId == $this->parentChannel && $clientId !== TeamspeakGateway::getOwnClientId()) {
            $client = TeamspeakGateway::getClientById($clientId);

            if (!$client || !isset($client['client_nickname'])) {
                return;
            }

            /** @phpstan-ignore-next-line */
            $clientIdentityId = $client['client_unique_identifier']->toString();
            if (isset($this->channelNameClientLists[$clientIdentityId])) {
                $channelName = $this->generateChannelName($this->channelNameClientLists[$clientIdentityId]);
            } else {
                $channelName = $this->generateChannelName($this->channelNames);
            }

            $newChannelId = TeamspeakGateway::createChannel(
                name: $channelName,
                codec: $this->channelCodec,
                parent: $this->parentChannel,
                maxClients: $this->channelClientLimit, neededTalkPower: $this->channelNeededTalkPowerPower,
                topic: $this->channelTopic, description: $this->channelDescription,
                codecQuality: $this->channelCodecQuality);

            if (!$newChannelId) {
                return;
            }

            TeamspeakGateway::moveClient($client->getId(), $newChannelId);
            $botChannel = $this->defaultChannel ?: $this->parentChannel;
            TeamspeakGateway::moveClient(TeamspeakGateway::getOwnClientId(), $botChannel);

            if ($this->channelAdminGroupId) {
                TeamspeakGateway::assignChannelGroupToClient($client, $newChannelId, $this->channelAdminGroupId);
            }

            $this->assignChannelPermissions($newChannelId);
        }
    }

    protected function generateChannelName(array $listOfNames, string $channelName = ''): string
    {
        $attempts = 0;

        do {
            $channelName .= $listOfNames[array_rand($listOfNames)];
            if ($attempts >= count($this->channelNames)) {
                $this->generateChannelName($listOfNames, $channelName);
            }

            $attempts++;
        } while (!TeamspeakGateway::channelExists($channelName));

        return $channelName;
    }

    protected function assignChannelPermissions(int $channelId): void
    {
        if ($this->channelNeededJoinPower) {
            TeamspeakGateway::assignPermissionToChannel($channelId,
                TeamspeakGateway::CHANNEL_PERMISSION_NEEDED_JOIN_POWER, $this->channelNeededJoinPower);
        }

        if ($this->channelNeededSubscribePower) {
            TeamspeakGateway::assignPermissionToChannel($channelId,
                TeamspeakGateway::CHANNEL_PERMISSION_NEEDED_SUBSCRIBE_POWER, $this->channelNeededSubscribePower);
        }

        if ($this->channelNeededDescriptionViewPower) {
            TeamspeakGateway::assignPermissionToChannel($channelId,
                TeamspeakGateway::CHANNEL_PERMISSION_NEEDED_DESCRIPTION_VIEW_POWER,
                $this->channelNeededDescriptionViewPower);
        }

        if ($this->channelNeededModifyPowerPower) {
            TeamspeakGateway::assignPermissionToChannel($channelId,
                TeamspeakGateway::CHANNEL_PERMISSION_NEEDED_MODIFY_POWER, $this->channelNeededModifyPowerPower);
        }
    }
}
