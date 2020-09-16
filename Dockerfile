FROM alpine as scoper
WORKDIR /bin
RUN apk add --no-cache wget && \
    wget -O php-scoper.phar https://github.com/humbug/php-scoper/releases/download/0.13.2/php-scoper.phar && \
    chmod +x php-scoper.phar

FROM php:7.4-cli-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=scoper /bin/php-scoper.phar /usr/bin/php-scoper
COPY . /app

RUN apk add --no-cache git zip unzip && \
    install-php-extensions bcmath gd intl mysqli pdo_mysql sockets bz2 gmp soap zip gmp && \
    composer install -d /app

ENTRYPOINT [ "/app/bin/pluginupload" ]
