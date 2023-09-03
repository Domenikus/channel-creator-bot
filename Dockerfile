FROM php:8.1-cli-alpine

ARG version

COPY . /usr/src/channel-creator-bot/

RUN apk add ncurses composer \
    && $(php -r '$extensionInstalled = array_map("strtolower", \get_loaded_extensions(false));$requiredExtensions = ["zlib", "phar", "openssl", "pcre", "tokenizer"];$extensionsToInstall = array_diff($requiredExtensions, $extensionInstalled);if ([] !== $extensionsToInstall) {echo \sprintf("docker-php-ext-install %s", implode(" ", $extensionsToInstall));}echo "echo \"No extensions\"";') \
    && php /usr/bin/composer.phar install --working-dir=/usr/src/channel-creator-bot --no-dev --no-scripts \
    && chmod 755 /usr/src/channel-creator-bot/vendor/laravel-zero/framework/bin/box \
    && php /usr/src/channel-creator-bot/channel-creator-bot app:build --build-version=${version} \
    && php /usr/bin/composer.phar dump-autoload --working-dir=/usr/src/channel-creator-bot --classmap-authoritative --no-dev -vvv --optimize \
    && mkdir -p /app  \
    && mkdir -p /app/storage  \
    && mkdir -p /app/views  \
    && cp /usr/src/channel-creator-bot/builds/channel-creator-bot /app  \
    && cp /usr/src/channel-creator-bot/entrypoint.sh /app  \
    && rm -R /usr/src/channel-creator-bot \
    && chmod +x /app/entrypoint.sh

WORKDIR /app
ENTRYPOINT ["/bin/sh", "/app/entrypoint.sh"]
