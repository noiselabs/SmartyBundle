ARG PHP_VERSION
ARG SYMFONY_VERSION

FROM php:${PHP_VERSION}-cli-alpine

LABEL maintainer="Vitor Brandao <vitor@noiselabs.io>"

RUN apk add --no-cache --virtual smarty-bundle-deps icu-dev
RUN docker-php-ext-install intl && docker-php-ext-enable intl

WORKDIR /app

COPY . /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV SYMFONY_REQUIRE=${SYMFONY_REQUIRE}

RUN SYMFONY_REQUIRE=${SYMFONY_REQUIRE} composer update --no-interaction --no-progress --prefer-dist --verbose

RUN composer install --prefer-dist --no-progress --no-interaction --verbose;
