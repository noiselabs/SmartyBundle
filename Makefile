SHELL := /usr/bin/env bash
PWD = $(shell pwd)
ROOT_DIR := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))

DOCKER_COMPOSE = cd Tests/Sandbox && COMPOSE_DOCKER_CLI_BUILD=0 COMPOSE_PROJECT_NAME=smartybundle docker-compose
DOCKER_COMPOSE_PROD = $(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.prod.yml
DC_BUILD_OPTS = --pull

default: help

help: ## The help text you're reading
	@grep --no-filename -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

### Docker-Compose ###
dc-build-all: ## Builds all the Docker images
	$(MAKE) dc-build-php8-sf5
	$(MAKE) dc-build-php8-sf4
	$(MAKE) dc-build-php7-sf5
	$(MAKE) dc-build-php7-sf4
	$(MAKE) dc-build-php7-sf3
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

dc-build-php7-sf3: ## Builds the php7-sf3 Docker images
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE_PROD) build $(DC_BUILD_OPTS)
.PHONY: dc-build-php7-sf3

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

dc-up-php7-sf4: ## Start the php7-sf3 docker hub
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) up
.PHONY: dc-up-php7-sf4

dc-up-php7-sf3: ## Start the php7-sf3 docker hub in detached mode (no logs)
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE) up --detach
.PHONY: dc-up-php7-sf3

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

dc-run-php7-sf3: ## Get a shell in a php7-sf3 container
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE) run --rm php bash
.PHONY: dc-run-php7-sf3

### Docker targets ###
build-php71: ## Build the PHP 7.1 container
	docker build . -f docker/7.1/Dockerfile -t noiselabs/smarty-bundle:latest-php7.1

build-php72: ## Build the PHP 7.2 container
	docker build . -f docker/7.2/Dockerfile -t noiselabs/smarty-bundle:latest-php7.2

build-php73: ## Build the PHP 7.3 container
	docker build . -f docker/7.3/Dockerfile -t noiselabs/smarty-bundle:latest-php7.3

build-php80: ## Build the PHP 8.0 container
	docker build . -f docker/8.0/Dockerfile -t noiselabs/smarty-bundle:latest-php8.0

build: ## Build Docker containers
	$(MAKE) build-php71 build-php72 build-php73 build-php80

build-parallel: ## Build Docker containers in parallel
	$(MAKE) -j4 build-php71 build-php72 build-php73 build-php80

test-php71: ## Run unit and functional tests in the PHP 7.1 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.1 composer test

test-php72: ## Run unit and functional tests in the PHP 7.2 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.2 composer test

test-php73: ## Run unit and functional tests in the PHP 7.3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.3 composer test

test-php80: ## Run unit and functional tests in the PHP 8.0 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php8.0 composer test

test: ## Run unit and functional tests
	$(MAKE) test-php70 test-php71 test-php72 test-php73

test-parallel: ## Run unit and functional tests in parallel
	$(MAKE) -j4 test-php71 test-php72 test-php73 test-php80

sh-php71: ## Get a shell in the PHP 7.1 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.1 sh

sh-php72: ## Get a shell in the PHP 7.2 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.2 sh

sh-php73: ## Get a shell in the PHP 7.3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.3 sh

sh-php80: ## Get a shell in the PHP 8.0 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php8.0 sh

web-php71: ## Launches the built-in PHP web server in the PHP 7.1 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9071:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.1 php -S 0.0.0.0:8080 -t web

web-php72: ## Launches the built-in PHP web server in the PHP 7.2 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9072:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.2 php -S 0.0.0.0:8080 -t web

web-php73: ## Launches the built-in PHP web server in the PHP 7.3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9073:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.3 php -S 0.0.0.0:8080 -t web

web-php80: ## Launches the built-in PHP web server in the PHP 8.0 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9080:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php8.0 php -S 0.0.0.0:8080 -t web

### Coding Standards ###
php-cs-fixer-dry-run: ## Check for PHP Coding Standards fixes but don't apply changes
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --dry-run --diff -v
.PHONY: php-cs-fixer-dry-run

php-cs-fixer-apply: ##  Run the PHP Coding Standards Fixer (PHP CS Fixer)
	tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff -v
.PHONY: php-cs-fixer-apply
