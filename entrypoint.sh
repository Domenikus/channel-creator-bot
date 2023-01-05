#!/bin/sh

php /app/channel-creator-bot migrate --force
php /app/channel-creator-bot run
