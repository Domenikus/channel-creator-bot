<?php

use Illuminate\Support\Str;

return [
    'ip' => env('TEAMSPEAK_IP', ''),
    'port' => env('TEAMSPEAK_PORT', 9987),
    'query_user' => env('TEAMSPEAK_QUERY_USER', ''),
    'query_password' => env('TEAMSPEAK_QUERY_PASSWORD', ''),
    'query_port' => env('TEAMSPEAK_QUERY_PORT', 10011),
    'bot_name' => 'channel-creator-bot_'.Str::random(4),
    'default_channel' => env('TEAMSPEAK_DEFAULT_CHANNEL'),

    'parent_channel' => env('TEAMSPEAK_PARENT_CHANNEL', 1),
    'channel_template' => env('TEAMSPEAK_CHANNEL_TEMPLATE', 'Team channel from:'),
    'channel_client_limit' => env('TEAMSPEAK_CHANNEL_CLIENT_LIMIT'),
    'channel_admin_group_id' => env('TEAMSPEAK_CHANNEL_ADMIN_GROUP_ID'),
    'channel_needed_join_power' => env('TEAMSPEAK_NEEDED_CHANNEL_JOIN_POWER'),
    'channel_needed_subscribe_power' => env('TEAMSPEAK_NEEDED_CHANNEL_SUBSCRIBE_POWER'),
];
