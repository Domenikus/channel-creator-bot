<?php

namespace App\Services;

use App\Services\Gateways\TeamspeakGateway;

class ClientService implements ClientServiceInterface
{
    protected ?int $channelAdminGroupId;

    protected ?int $channelClientLimit;

    protected ?int $channelNeededJoinPower;

    protected ?int $channelNeededSubscribePower;

    protected string $channelTemplate;

    protected int $parentChannel;

    public function __construct(int $parentChannel, string $channelTemplate, ?int $channelClientLimit = null, ?int $channelAdminGroupId = null, ?int $channelNeededJoinPower = null, ?int $channelNeededSubscribePower = null)
    {
        $this->parentChannel = $parentChannel;
        $this->channelTemplate = $channelTemplate;
        $this->channelClientLimit = $channelClientLimit;
        $this->channelAdminGroupId = $channelAdminGroupId;
        $this->channelNeededJoinPower = $channelNeededJoinPower;
        $this->channelNeededSubscribePower = $channelNeededSubscribePower;
    }

    public function handleClientMove(int $clientId, int $targetChannelId): void
    {
        if ($targetChannelId == $this->parentChannel && $clientId !== TeamspeakGateway::getOwnClientId()) {
            $client = TeamspeakGateway::getClientById($clientId);

            if (! $client || ! isset($client['client_nickname'])) {
                return;
            }

            $newChannelId = TeamspeakGateway::createChannel($this->channelTemplate.' '.$client['client_nickname'], $this->parentChannel, $this->channelClientLimit);

            if (! $newChannelId) {
                return;
            }

            TeamspeakGateway::moveClient($client->getId(), $newChannelId);
            TeamspeakGateway::moveClient(TeamspeakGateway::getOwnClientId(), $this->parentChannel);

            if ($this->channelAdminGroupId) {
                TeamspeakGateway::assignChannelGroupToClient($client, $newChannelId, $this->channelAdminGroupId);
            }

            if ($this->channelNeededJoinPower) {
                TeamspeakGateway::assignPermissionToChannel($newChannelId, TeamspeakGateway::CHANNEL_PERMISSION_NEEDED_JOIN_POWER, $this->channelNeededJoinPower);
            }

            if ($this->channelNeededSubscribePower) {
                TeamspeakGateway::assignPermissionToChannel($newChannelId, TeamspeakGateway::CHANNEL_PERMISSION_NEEDED_SUBSCRIBE_POWER, $this->channelNeededSubscribePower);
            }
        }
    }
}
