# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION
ARG NGINX_VERSION=1.21

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine3.12 AS symfony_php

RUN apk update

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
    	bash \
		fcgi \
		file \
		gettext \
		git \
		gnu-libiconv \
    	postgresql-client \
	;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

ARG APCU_VERSION=5.1.20
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
    	postgresql-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
      	pdo_pgsql \
		zip \
	; \
    pecl channel-update pecl.php.net ; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# Allow to choose skeleton
ARG SKELETON="symfony/skeleton"
ENV SKELETON ${SKELETON}

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY}

# Allow to select skeleton version
ARG SYMFONY_VERSION
ENV SYMFONY_REQUIRE=${SYMFONY_VERSION}
ENV SYMFONY_VERSION=${SYMFONY_VERSION}

# Download the Symfony skeleton and leverage Docker cache layers
RUN composer create-project "${SKELETON} ${SYMFONY_VERSION}.99" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-interaction --no-scripts --verbose

###> extra packages ###
RUN composer require --no-interaction --no-scripts --with-all-dependencies --verbose \
    annotations \
    sensio/framework-extra-bundle:*
###< extra packages ###

###> recipes ###
###< recipes ###

COPY app/sf-${SYMFONY_VERSION} .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction --verbose; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync

RUN php bin/console debug:router

VOLUME /srv/app/var

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:${NGINX_VERSION}-alpine AS symfony_nginx

COPY docker/nginx/templates/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

WORKDIR /srv/app/public

COPY --from=symfony_php /srv/app/public ./
