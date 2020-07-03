FROM php:7.4-cli-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY . /app

RUN apk add --no-cache git zip unzip && \
    install-php-extensions zip intl && \
    composer install -d /app

ENTRYPOINT [ "/app/bin/pluginupload" ]
