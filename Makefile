SHELL := /usr/bin/env bash
PWD = $(shell pwd)
ROOT_DIR := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))

DOCKER_COMPOSE = cd tests/Sandbox/Compose && COMPOSE_DOCKER_CLI_BUILD=0 COMPOSE_PROJECT_NAME=smartybundle docker-compose
DOCKER_COMPOSE_PROD = $(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.prod.yml
DC_BUILD_OPTS = --pull

default: help

help: ## The help text you're reading
	@grep --no-filename -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

install: ## Install required tools
	cd tools/php-cs-fixer && composer install
.PHONY: install

### Sandbox/Compose Docker-Compose targets ###
dc-build: ## Builds all the Docker-Compose images (in parallel)
	$(MAKE) -j4 dc-build-php7-sf4 dc-build-php7-sf5 dc-build-php8-sf4 dc-build-php8-sf5
	docker images | grep smartybundle-sandbox
.PHONY: dc-build-all

dc-build-php8-sf5: ## Builds the php8-sf5 Docker images
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php8-sf5

dc-build-php8-sf4: ## Builds the php8-sf4 Docker images
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php8-sf4

dc-build-php7-sf5: ## Builds the php7-sf5 Docker images
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php7-sf5

dc-build-php7-sf4: ## Builds the php7-sf4 Docker images
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php7-sf4

dc-config: ## Dumps the default docker-compose config
	@$(DOCKER_COMPOSE) config
.PHONY: dc-config

dc-up-php8-sf5: ## Start the php8-sf5 docker hub
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) up
.PHONY: dc-up-php7-sf4

dc-up-php8-sf4: ## Start the php8-sf4 docker hub
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) up
.PHONY: dc-up-php7-sf4

dc-up-php7-sf5: ## Start the php7-sf5 docker hub
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) up
.PHONY: dc-up-php7-sf4

dc-up-php7-sf4: ## Start the php7-sf4 docker hub
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) up
.PHONY: dc-up-php7-sf4

dc-run-php8-sf5: ## Get a shell in a php8-sf5 container
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php8-sf5

dc-run-php8-sf4: ## Get a shell in a php8-sf4 container
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php8-sf4

dc-run-php7-sf4: ## Get a shell in a php7-sf4 container
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php7-sf4

### Sandbox/DockerTesting targets ###
build-php7-sf4: ## Build the php7-sf4 test image
	docker build . -f tests/Sandbox/DockerTesting/Dockerfile --no-cache --build-arg PHP_VERSION=7.4 --build-arg SYMFONY_VERSION=4.4 --build-arg COMPOSER_FLAGS="--prefer-lowest" -t noiselabs/smarty-bundle-testing:php7-sf4
.PHONY: build-php7-sf4

build-php7-sf5: ## Build the php7-sf5 test image
	docker build . -f tests/Sandbox/DockerTesting/Dockerfile --no-cache --build-arg PHP_VERSION=7.4 --build-arg SYMFONY_VERSION=5.3 -t noiselabs/smarty-bundle-testing:php7-sf5
.PHONY: build-php7-sf5

build-php8-sf4: ## Build the php8-sf4 test image
	docker build . -f tests/Sandbox/DockerTesting/Dockerfile --no-cache --build-arg PHP_VERSION=8.0 --build-arg SYMFONY_VERSION=4.4 -t noiselabs/smarty-bundle-testing:php8-sf4
.PHONY: build-php8-sf4

build-php8-sf5: ## Build the php8-sf5 test image
	docker build . -f tests/Sandbox/DockerTesting/Dockerfile --no-cache --build-arg PHP_VERSION=8.0 --build-arg SYMFONY_VERSION=5.3 -t noiselabs/smarty-bundle-testing:php8-sf5
.PHONY: build-php8-sf5

build: ## Build Docker images (in parallel)
	$(MAKE) -j4 build-php7-sf4 build-php7-sf5 build-php8-sf4 build-php8-sf5
.PHONY: build

test-php7-sf4: ## Run phpunit tests in the php7-sf4 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php7-sf4 composer test
.PHONY: test-php7-sf4

test-php7-sf5: ## Run phpunit tests in the php7-sf5 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php7-sf5 composer test
.PHONY: test-php7-sf5

test-php8-sf4: ## Run phpunit tests in the php8-sf4 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php8-sf4 composer test
.PHONY: test-php8-sf4

test-php8-sf5: ## Run phpunit tests in the php8-sf5 image
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle-testing:php8-sf5 composer test
.PHONY: test-php8-sf5

test: ## Run phpunit tests
	$(MAKE) test-php7-sf4 test-php7-sf5 test-php8-sf4 test-php8-sf5
.PHONY: test

test-parallel: ## Run phpunit tests in parallel
	$(MAKE) -j4 test-php7-sf4 test-php7-sf5 test-php8-sf4 test-php8-sf5
.PHONY: test-parallel

sh-php7-sf4: ## Get a shell in the php7-sf4 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php7-sf4 sh
.PHONY: sh-php7-sf4

sh-php7-sf5: ## Get a shell in the php7-sf5 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php7-sf5 sh
.PHONY: sh-php7-sf5

sh-php8-sf4: ## Get a shell in the php8-sf4 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php8-sf4 sh
.PHONY: sh-php8-sf4

sh-php8-sf5: ## Get a shell in the php8-sf5 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php8-sf5 sh
.PHONY: sh-php8-sf5

web-php7-sf4: ## Launches the built-in PHP web server in the php7-sf4 container
	@echo http://localhost:8074/
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8074:8080/tcp -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php7-sf4 php -S 0.0.0.0:8080 -t web
.PHONY: web-php7-sf4

web-php7-sf5: ## Launches the built-in PHP web server in the php7-sf5 container
	@echo http://localhost:8075/
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8075:8080/tcp -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php7-sf5 php -S 0.0.0.0:8080 -t web
.PHONY: web-php7-sf5

web-php8-sf4: ## Launches the built-in PHP web server in the php8-sf4 container
	@echo http://localhost:8084/
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8084:8080/tcp -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php8-sf4 php -S 0.0.0.0:8080 -t web
.PHONY: web-php8-sf4

web-php8-sf5: ## Launches the built-in PHP web server in the php8-sf5 container
	@echo http://localhost:8085/
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:8085:8080/tcp -w /app/tests/Sandbox/App noiselabs/smarty-bundle-testing:php8-sf5 php -S 0.0.0.0:8080 -t web
.PHONY: web-php8-sf5

### Coding Standards ###
php-cs-fixer-dry-run: ## Check for PHP Coding Standards fixes but don't apply changes
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --dry-run --diff -v
.PHONY: php-cs-fixer-dry-run

php-cs-fixer-apply: ##  Run the PHP Coding Standards Fixer (PHP CS Fixer)
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff -v
.PHONY: php-cs-fixer-apply

### Composer ###
composer-sf4-lowest: ## Install composer dependencies using sf4 --prefer-lowest
	SYMFONY_REQUIRE=4.4 composer update --prefer-dist --prefer-lowest
.PHONY: composer-sf3

composer-sf4: ## Install composer dependencies using sf4
	SYMFONY_REQUIRE=4.4 composer update --prefer-dist
.PHONY: composer-sf4

composer-sf5: ## Install composer dependencies using sf5
	SYMFONY_REQUIRE=5.3 composer update --prefer-dist
.PHONY: composer-sf5

### PHPUnit tests ###
phpunit: ## Run PHPUnit tests
	XDEBUG_MODE=coverage phpunit --debug --coverage-text --coverage-html=.phpunit.cache/html
.PHONY: phpunit