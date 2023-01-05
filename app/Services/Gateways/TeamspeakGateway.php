<?php

namespace App\Services\Gateways;

use App\Facades\TeamSpeak3;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use TeamSpeak3_Node_Client;
use TeamSpeak3_Node_Servergroup;

class TeamspeakGateway
{
    public static function assignServerGroupIcon(int $serverGroupId, int $iconId): bool
    {
        $iconPermissionId = self::getPermissionIdByName('i_icon_id');
        if (! $iconPermissionId) {
            Log::error('Icon permission not found');

            return false;
        }

        return self::assignServerGroupPermission($serverGroupId, $iconPermissionId, $iconId);
    }

    public static function assignServerGroupPermission(int $serverGroupId, int $permissionId, int $value): bool
    {
        $success = false;

        try {
            TeamSpeak3::serverGroupPermAssign($serverGroupId, $permissionId, $value);
            $success = true;
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['serverGroupId' => $serverGroupId, 'permissionId' => $permissionId, 'value' => $value]);
            report($e);
        }

        return $success;
    }

    public static function assignServerGroupSortId(int $serverGroupId, int $sortIdValue): bool
    {
        $sortIdPermissionId = self::getPermissionIdByName('i_group_sort_id');
        if (! $sortIdPermissionId) {
            Log::error('Sort id permission not found');

            return false;
        }

        return self::assignServerGroupPermission($serverGroupId, $sortIdPermissionId, $sortIdValue);
    }

    public static function assignServerGroupToClient(TeamSpeak3_Node_Client $client, int $serverGroupId): bool
    {
        try {
            $client->addServerGroup($serverGroupId);

            return true;
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['client' => $client, 'serverGroupId' => $serverGroupId]);
            report($e);
        }

        return false;
    }

    public static function calculateIconId(string $data): int
    {
        return crc32($data);
    }

    public static function clearClientCache(): void
    {
        TeamSpeak3::clientListReset();
    }

    public static function createServerGroup(string $name): ?int
    {
        $serverGroupId = null;

        try {
            $serverGroupId = TeamSpeak3::serverGroupCreate($name);
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['name' => $name]);
            report($e);
        }

        return $serverGroupId;
    }

    public static function deleteServerGroup(int $id): bool
    {
        $result = false;

        try {
            TeamSpeak3::serverGroupDelete($id, true);
            $result = true;
        } catch (Exception $e) {
            Log::error('Could not delete server group', ['id' => $id]);
            report($e);
        }

        return $result;
    }

    /**
     * @return TeamSpeak3_Node_Client[]
     */
    public static function getActiveClients(): array
    {
        $clientList = [];

        try {
            $clientList = TeamSpeak3::clientList();
        } catch (Exception $e) {
            report($e);
        }

        return $clientList;
    }

    public static function getAvailablePermissions(): array
    {
        $permissions = [];

        try {
            $permissionsList = TeamSpeak3::permissionList();
            $permissions = Arr::pluck($permissionsList, 'permid', 'permname');
        } catch (Exception $e) {
            report($e);
        }

        return $permissions;
    }

    public static function getClient(string $clientId): ?TeamSpeak3_Node_Client
    {
        $result = null;

        try {
            $result = TeamSpeak3::clientGetByUid($clientId);
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['clientId' => $clientId]);
            report($e);
        }

        return $result;
    }

    public static function getPermissionIdByName(string $name): ?int
    {
        $permissionId = null;

        try {
            $permissionId = TeamSpeak3::permissionGetIdByName($name);
        } catch (Exception $e) {
        }

        return $permissionId;
    }

    public static function getServerGroupByName(string $name): ?TeamSpeak3_Node_Servergroup
    {
        $serverGroup = null;

        try {
            $serverGroup = TeamSpeak3::serverGroupGetByName($name);
        } catch (Exception $e) {
        }

        return $serverGroup;
    }

    public static function getServerGroupHighestSortId(): int
    {
        $highestServerGroupId = 10;
        foreach (self::getServerGroups() as $serverGroup) {
            $serverGroupId = $serverGroup->getId();
            if ($serverGroupId > 10) {
                $sortId = self::getServerGroupSortId($serverGroupId);
                if ($sortId && $sortId > $highestServerGroupId) {
                    $highestServerGroupId = $sortId;
                }
            }
        }

        return $highestServerGroupId;
    }

    public static function getServerGroupSortId(int $serverGroupId): ?int
    {
        $sortId = null;

        $sortIdPermissionId = self::getPermissionIdByName('i_group_sort_id');
        try {
            $permissionList = TeamSpeak3::serverGroupPermList($serverGroupId);
            foreach ($permissionList as $permission) {
                if ($permission['permid'] == $sortIdPermissionId) {
                    $sortId = $permission['permvalue'];
                }
            }
        } catch (Exception $e) {
            Log::error('Could not get server group permission list', ['serverGroupId' => $serverGroupId]);
            report($e);
        }

        return $sortId;
    }

    /**
     * @return array<TeamSpeak3_Node_Servergroup>
     */
    public static function getServerGroups(): array
    {
        $serverGroups = [];

        try {
            $serverGroups = TeamSpeak3::serverGroupList();
        } catch (Exception $e) {
            Log::error('Could not get server groups from ts3 server');
            report($e);
        }

        return $serverGroups;
    }

    /**
     * @param  TeamSpeak3_Node_Client  $client
     * @return array<string>
     */
    public static function getServerGroupsAssignedToClient(TeamSpeak3_Node_Client $client): array
    {
        try {
            $actualServerGroups = [];
            $actualGroups = $client->memberOf();
            foreach ($actualGroups as $group) {
                if (isset($group['sgid'])) {
                    $actualServerGroups[] = $group['sgid'];
                }
            }
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['client' => $client]);
            report($e);
        }

        return $actualServerGroups;
    }

    public static function getServerIcons(): array
    {
        $serverIcons = [];

        try {
            $serverIcons = TeamSpeak3::channelFileList(0, '', '/icons');
        } catch (Exception $e) {
            report($e);
        }

        return $serverIcons;
    }

    public static function iconExists(int $id): bool
    {
        $result = false;

        foreach (self::getServerIcons() as $serverIcon) {
            if ($serverIcon['name'] == 'icon_'.$id) {
                $result = true;
            }
        }

        return $result;
    }

    public static function refreshConnection(array $properties = []): void
    {
        TeamSpeak3::selfUpdate($properties);
    }

    public static function removeServerGroupFromClient(TeamSpeak3_Node_Client $client, int $serverGroupId): bool
    {
        try {
            $client->remServerGroup($serverGroupId);

            return true;
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['client' => $client, 'serverGroupId' => $serverGroupId]);
            report($e);
        }

        return false;
    }

    public static function sendMessageToClient(TeamSpeak3_Node_Client $client, string $message): bool
    {
        try {
            $client->message($message);

            return true;
        } catch (Exception $e) {
            Log::debug($e->getMessage(), ['client' => $client, 'message' => $message]);
            report($e);
        }

        return false;
    }

    public static function uploadIcon(string $data): ?int
    {
        $iconId = null;

        try {
            $iconId = TeamSpeak3::iconUpload($data);
        } catch (Exception $e) {
            report($e);
        }

        return $iconId;
    }
}
