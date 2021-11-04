ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli-alpine

LABEL maintainer="Vitor Brandao <vitor@noiselabs.io>"

RUN apk add --no-cache --virtual smartybundle-deps icu-dev
RUN docker-php-ext-install intl && docker-php-ext-enable intl

WORKDIR /app

COPY . /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

ARG SYMFONY_VERSION
ENV SYMFONY_REQUIRE=${SYMFONY_VERSION}
ARG COMPOSER_FLAGS=""

RUN composer global require --no-progress --no-scripts --no-plugins --ignore-platform-req=php symfony/flex

RUN composer update --no-interaction --no-progress --prefer-dist --verbose --ignore-platform-req=php ${COMPOSER_FLAGS}

RUN php -v