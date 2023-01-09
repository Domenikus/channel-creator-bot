<?php

namespace App\Services\Listeners;

use App\Facades\TeamSpeak3;
use App\Services\ClientServiceInterface;
use TeamSpeak3_Adapter_ServerQuery_Event;
use TeamSpeak3_Helper_Signal;
use TeamSpeak3_Node_Host;

class ChannelListener implements TeamspeakListener
{
    protected ClientServiceInterface $service;

    public function __construct(ClientServiceInterface $service)
    {
        $this->service = $service;
    }

    public function init(): void
    {
        TeamSpeak3::notifyRegister('channel');

        TeamSpeak3_Helper_Signal::getInstance()->subscribe('notifyClientmoved',
            function (TeamSpeak3_Adapter_ServerQuery_Event $event, TeamSpeak3_Node_Host $host) {
                $data = $event->getData();
                $this->service->handleClientMove((int) $data['clid'], (int) $data['ctid']);
            });
    }
}
