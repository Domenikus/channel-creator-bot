<?php

namespace App\Services\Gateways;

use App\Facades\TeamSpeak3;
use Exception;
use Illuminate\Support\Facades\Log;

class TeamspeakGateway
{
    /**
     * @param  int  $cid Channel ID
     * @param  int|null  $clid Client ID
     * @param  string|null  $cpw Channel Password
     * @return bool
     */
    public static function moveClient(int $cid, int $clid = null, string $cpw = null): bool
    {
        $result = false;

        try {
            if ($clientId = self::getOwnClientId()) {
                TeamSpeak3::clientMove($clientId, $cid, $cpw);
                $result = true;
            }
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['client_id' => $clid, 'channel_id' => $cid, 'channel_password' => $cpw]);
            report($e);
        }

        return $result;
    }

    public static function getOwnClientId(): ?int
    {
        $clientId = null;

        try {
            $clientIdResponse = TeamSpeak3::whoamiGet('client_id');
            if (is_numeric($clientIdResponse)) {
                $clientId = (int) $clientIdResponse;
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            report($e);
        }

        return $clientId;
    }

    public static function refreshConnection(array $properties = []): void
    {
        TeamSpeak3::selfUpdate($properties);
    }
}
