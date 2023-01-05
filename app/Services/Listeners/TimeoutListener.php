<?php

namespace App\Services\Listeners;

use App\Facades\TeamSpeak3;
use App\Services\Gateways\TeamspeakGateway;
use Illuminate\Support\Facades\Log;
use TeamSpeak3_Adapter_ServerQuery;
use TeamSpeak3_Helper_Signal;

class TimeoutListener implements TeamspeakListener
{
    protected int $lastUpdate;

    public function __construct()
    {
        $this->lastUpdate = time();
    }

    public function init(): void
    {
        TeamSpeak3::notifyRegister('server');

        TeamSpeak3_Helper_Signal::getInstance()->subscribe('serverqueryWaitTimeout',
            function ($seconds, TeamSpeak3_Adapter_ServerQuery $adapter) {
                if ($adapter->getQueryLastTimestamp() < time() - 180) {
                    Log::info('No reply from the server for '.$seconds.' seconds. Sending keep alive command.');
                    TeamspeakGateway::refreshConnection();
                }
            });
    }
}
