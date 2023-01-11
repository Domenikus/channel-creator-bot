FROM php:8.1-cli-alpine

ARG version

RUN apk add ncurses composer
RUN mkdir -p /usr/src/channel-creator-bot

COPY . /usr/src/channel-creator-bot/

RUN php /usr/bin/composer.phar install --working-dir=/usr/src/channel-creator-bot --no-scripts
RUN chmod 755 /usr/src/channel-creator-bot/vendor/laravel-zero/framework/bin/box
RUN php /usr/src/channel-creator-bot/channel-creator-bot app:build --build-version=${version}
RUN php /usr/bin/composer.phar dump-autoload --working-dir=/usr/src/channel-creator-bot --classmap-authoritative --no-dev -vvv --optimize

RUN mkdir -p /app  \
    && mkdir -p /app/storage  \
    && mkdir -p /app/views  \
    && cp /usr/src/channel-creator-bot/builds/channel-creator-bot /app  \
    && cp /usr/src/channel-creator-bot/entrypoint.sh /app  \
    && rm -R /usr/src/channel-creator-bot
RUN chmod +x /app/entrypoint.sh

WORKDIR /app
ENTRYPOINT ["/bin/sh", "/app/entrypoint.sh"]
