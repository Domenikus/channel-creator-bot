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

    'watched_channels' => env('TEAMSPEAK_WATCHED_CHANNELS', ''),
    'channel_template' => env('TEAMSPEAK_CHANNEL_TEMPLATE', 'Team channel from:'),
    'channel_admin' => env('TEAMSPEAK_CHANNEL_ADMIN', false),
];
