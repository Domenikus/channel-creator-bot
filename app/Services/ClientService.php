<?php

namespace App\Services;

use App\Services\Gateways\TeamspeakGateway;

class ClientService implements ClientServiceInterface
{
    protected ?int $channelAdminGroupId;

    protected ?int $channelClientLimit;

    protected array $channelNames;

    protected ?int $defaultChannel;

    protected ?int $channelNeededJoinPower;

    protected ?int $channelNeededSubscribePower;

    protected int $parentChannel;

    public function __construct(int $parentChannel, array $channelNames, ?int $defaultChannel, ?int $channelClientLimit = null, ?int $channelAdminGroupId = null, ?int $channelNeededJoinPower = null, ?int $channelNeededSubscribePower = null)
    {
        $this->parentChannel = $parentChannel;
        $this->channelNames = $channelNames;
        $this->defaultChannel = $defaultChannel;
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

            $newChannelId = TeamspeakGateway::createChannel($this->generateChannelName(), $this->parentChannel, $this->channelClientLimit);
            if (! $newChannelId) {
                return;
            }

            TeamspeakGateway::moveClient($client->getId(), $newChannelId);
            $botChannel = $this->defaultChannel ?: $this->parentChannel;
            TeamspeakGateway::moveClient(TeamspeakGateway::getOwnClientId(), $botChannel);

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

    protected function generateChannelName(string $channelName = ''): string
    {
        $attempts = 0;

        do {
            $channelName .= $this->channelNames[array_rand($this->channelNames)];
            if ($attempts >= count($this->channelNames)) {
                $this->generateChannelName($channelName);
            }

            $attempts++;
        } while (! TeamspeakGateway::channelExists($channelName));

        return $channelName;
    }
}
