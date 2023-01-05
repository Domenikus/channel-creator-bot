<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use TeamSpeak3 as BaseTeamSpeak3;
use TeamSpeak3_Node_Abstract;
use TeamSpeak3_Node_Host;
use TeamSpeak3_Node_Server;

/**
 * @mixin TeamSpeak3_Node_Abstract
 * @mixin TeamSpeak3_Node_Host
 * @mixin TeamSpeak3_Node_Server
 */
class TeamSpeak3 extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BaseTeamSpeak3::class;
    }
}
