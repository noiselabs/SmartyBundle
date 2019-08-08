ARG BUILD__COMPOSER_VERSION=1.8
ARG BUILD__PHP_VERSION=7.0

# Composer stage
FROM composer:${BUILD__COMPOSER_VERSION} as composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/var/cache/composer
ENV COMPOSER_NO_INTERACTION=1

WORKDIR /app

COPY composer.json /app/

RUN composer global require hirak/prestissimo

# App stage
FROM php:${BUILD__PHP_VERSION}-cli-alpine

LABEL maintainer="Vitor Brandao <vitor@noiselabs.io>"

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/var/cache/composer
ENV COMPOSER_NO_INTERACTION=1

RUN apk add --no-cache --virtual smarty-bundle-deps icu-dev
RUN docker-php-ext-install intl && docker-php-ext-enable intl

WORKDIR /app

COPY . /app

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /var/cache/composer /var/cache/composer

RUN composer install --prefer-dist --optimize-autoloader --no-interaction

RUN composer --version && php -v && php vendor/bin/phpunit --version
