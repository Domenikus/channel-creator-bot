# Channel-creator-bot

Teamspeak bot which creates group-channel on demand. User just needs to join a predefined channel to create a group
channel.

![example.png](ressources%2Fexample.png)

## Setup

### Development

Copy .env.example file to .env file and fill necessary values

```
composer install
```

```
php channel-creator-bot migrate
```

## Run

Run the bot

```
php game-bot run
```

## Setup for production

- Create a query user on the teamspeak server, see necessary permission down below
- Whitelist the ipaddress of the bot or turn off anti-flood-protection
- Run bot setup

### Docker-compose example

```
version: "3.1"

services:
  channel-creator-bot:
    container_name: channel-creator-bot
    image: domenikus/channel-creator-bot
    depends_on:
      - mariadb
    restart: unless-stopped
    env_file:
      - .env
    volumes:
      - ./logs:/app/logs
```

### Example .env file

```
# IP of the teamspeak server
TEAMSPEAK_IP=

# Teampspeak port, default is 9987
TEAMSPEAK_PORT=9987

# Query username
TEAMSPEAK_QUERY_USER=

# Query user password
TEAMSPEAK_QUERY_PASSWORD=

# Query user port, default is 10011
TEAMSPEAK_QUERY_PORT=10011

# Bot default channel, if not provided bot will stay in servers default channel
TEAMSPEAK_DEFAULT_CHANNEL=

# Parent channel where group channels created below
TEAMSPEAK_PARENT_CHANNEL=

# Client limit for created sub channel
TEAMSPEAK_CHANNEL_CLIENT_LIMIT=

# Id of channel admin groups which will be assiged to client. If not provided no channel group will be assigned to client
TEAMSPEAK_CHANNEL_ADMIN_GROUP_ID=

# Needed join power to join the created channel, if not provided no join power is needed.
TEAMSPEAK_NEEDED_CHANNEL_JOIN_POWER=

# Needed subscribe power to subscribe the created channel, if not provided no subscribe power is needed.
TEAMSPEAK_NEEDED_CHANNEL_SUBSCRIBE_POWER=

#Specify how the application will log messages like erros. Default will be stack.
LOG_CHANNEL=stack

# Default is 'info' if you want to debug the application may you want to change this to 'debug'
LOG_LEVEL=info
```

### Necessary bot permissions in ts3 server

```
b_serverinstance_permission_list
b_virtualserver_client_list
b_virtualserver_notify_register
b_virtualserver_notify_unregister
b_channel_create_child
b_channel_create_temporary
b_channel_create_modify_with_codec_opusvoice
i_channel_create_modify_with_codec_latency_factor_min (0)
b_channel_create_with_maxclients
b_channel_join_permanent
b_channel_join_temporary
i_channel_needed_join_power (grant)
i_channel_needed_subscribe_power (grant)
i_channel_max_depth (-1)
i_channel_needed_permission_modify_power (grant)
b_virtualserver_channel_permission_list
i_group_member_add_power
i_group_member_add_power (grant)
i_group_member_remove_power
i_permission_modify_power
i_icon_id (grant)
b_group_is_permanent
i_group_sort_id
b_client_info_view
i_client_move_power
```

## Quality tools

### PHPStan (Code quality) via [Larastan](https://github.com/nunomaduro/larastan)

This command is used for analyzing your code quality.

`composer analyse`

For IDE integration refer [here](https://www.jetbrains.com/help/phpstorm/using-phpstan.html).

### PHP CS Fixer (Code style) via [Pint](https://laravel.com/docs/9.x/pint)

This command is used to show code style errors.

`composer sniff`

This command will try to auto fix your code.

`composer lint`

For IDE integration refer [here](https://gilbitron.me/blog/running-laravel-pint-in-phpstorm/).

## Contribute

Feel free to extend the functionality. Pull requests are welcome.
