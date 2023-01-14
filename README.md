# Channel-creator-bot

Teamspeak bot which creates group-channel on demand. User just needs to join a predefined channel to create a group
channel. All clients entering the specified channel get their own temporary channel as sub channel. Created channels are
named after countries in the world.

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
php channel-creator-bot run
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


