<?php

namespace App\Services;

use App\Services\Gateways\TeamspeakGateway;

class ClientService implements ClientServiceInterface
{
    protected bool $channelAdmin;

    protected string $channelTemplate;

    protected array $watchedChannels;

    public function __construct(array $watchedChannels, string $channelTemplate, bool $channelAdmin)
    {
        $this->watchedChannels = $watchedChannels;
        $this->channelTemplate = $channelTemplate;
        $this->channelAdmin = $channelAdmin;
    }

    public function handleClientMove(int $clientId, int $targetChannelId): void
    {
        if (in_array($targetChannelId, $this->watchedChannels) && $clientId !== TeamspeakGateway::getOwnClientId()) {
            // ToDo fancy stuff
        }
    }
}
