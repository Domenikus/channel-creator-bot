<?php

namespace App\Services\Gateways;

use App\Facades\TeamSpeak3;
use Exception;
use Illuminate\Support\Facades\Log;
use TeamSpeak3_Node_Client;

class TeamspeakGateway
{
    const CHANNEL_PERMISSION_NEEDED_JOIN_POWER = 'i_channel_needed_join_power';

    const CHANNEL_PERMISSION_NEEDED_MODIFY_POWER = 'i_channel_needed_permission_modify_power';

    const CHANNEL_PERMISSION_NEEDED_SUBSCRIBE_POWER = 'i_channel_needed_subscribe_power';

    public static function assignChannelGroupToClient(TeamSpeak3_Node_Client $client, int $channelId, int $channelGroupId): bool
    {
        $result = false;

        try {
            $client->setChannelGroup($channelId, $channelGroupId);

            $result = true;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['client' => $client, 'channelId' => $channelId, 'channelGroupId' => $channelGroupId]);
            report($e);
        }

        return $result;
    }

    public static function assignPermissionToChannel(int $channelId, string $permissionId, int $value): bool
    {
        $result = false;

        try {
            /** @phpstan-ignore-next-line */ // Function also accepts permission name
            TeamSpeak3::channelPermAssign($channelId, $permissionId, $value);
            $result = true;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['channelId' => $channelId, 'permissionId' => $permissionId, 'value' => $value]);
            report($e);
        }

        return $result;
    }

    public static function channelExists(string $name): bool
    {
        $result = true;

        try {
            TeamSpeak3::channelGetByName($name);
            $result = false;
        } catch (Exception $e) {
        }

        return $result;
    }

    public static function clearClientCache(): void
    {
        TeamSpeak3::clientListReset();
    }

    public static function createChannel(string $name, int $parent = null, int $maxClients = null, bool $permanent = false): ?int
    {
        $channelId = null;

        $channelProperties = [
            'channel_name' => $name,
            'channel_flag_permanent' => $permanent,
            'channel_codec' => \TeamSpeak3::CODEC_OPUS_VOICE,
        ];

        if ($parent) {
            $channelProperties['cpid'] = $parent;
        }

        if ($maxClients) {
            $channelProperties['channel_maxclients'] = $maxClients;
            $channelProperties['channel_flag_maxclients_unlimited'] = false;
        }

        try {
            $channelId = TeamSpeak3::channelCreate($channelProperties);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['name' => $name, 'parent' => $parent, 'permanent' => $permanent]);
            report($e);
        }

        return $channelId;
    }

    public static function getClientById(int $clientId): ?TeamSpeak3_Node_Client
    {
        $client = null;

        try {
            $client = TeamSpeak3::clientGetById($clientId);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['clientId' => $clientId]);
            report($e);
        }

        return $client;
    }

    public static function getOwnClientId(): int
    {
        $clientId = TeamSpeak3::whoamiGet('client_id');
        if (! is_numeric($clientId)) {
            throw new Exception('Could not get own client id');
        }

        return (int) $clientId;
    }

    public static function moveClient(int $clientId, int $channelId, string $channelPassword = null): bool
    {
        $result = false;

        try {
            TeamSpeak3::clientMove($clientId, $channelId, $channelPassword);
            $result = true;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['client_id' => $clientId, 'channel_id' => $channelId, 'channel_password' => $channelPassword]);
            report($e);
        }

        return $result;
    }

    public static function refreshConnection(array $properties = []): void
    {
        TeamSpeak3::selfUpdate($properties);
    }
}
