SHELL := /usr/bin/env bash
PWD = $(shell pwd)
ROOT_DIR := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))

DOCKER_COMPOSE = cd Tests/Functional/symfony-docker && COMPOSE_DOCKER_CLI_BUILD=0 COMPOSE_PROJECT_NAME=smartybundle docker-compose
DOCKER_COMPOSE_PROD = $(DOCKER_COMPOSE) -f docker-compose.yml -f docker-compose.prod.yml
DC_BUILD_OPTS = --pull

default: help

help: ## The help text you're reading
	@grep --no-filename -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

### Docker-Compose & Docker targets ###
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

dc-up-php8-sf5: ## Start the php8-sf5 docker hub in detached mode (no logs)
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) up --detach
.PHONY: dc-up-php7-sf4

dc-up-php8-sf4: ## Start the php8-sf4 docker hub in detached mode (no logs)
	export PHP_VERSION=8.0 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE) up --detach
.PHONY: dc-up-php7-sf4

dc-up-php7-sf5: ## Start the php7-sf5 docker hub in detached mode (no logs)
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=5.3 && \
	$(DOCKER_COMPOSE) up --detach
.PHONY: dc-up-php7-sf4

dc-up-php7-sf4: ## Start the php7-sf3 docker hub in detached mode (no logs)
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=4.4 && \
	$(DOCKER_COMPOSE_PROD) up --detach
.PHONY: dc-up-php7-sf4

dc-up-php7-sf3: ## Start the php7-sf3 docker hub in detached mode (no logs)
	export PHP_VERSION=7.4 && \
	export SYMFONY_VERSION=3.4 && \
	$(DOCKER_COMPOSE) up --detach
.PHONY: dc-up-php7-sf3

dc-logs: ## Show live logs
	@$(DOCKER_COMPOSE) logs --tail=0 --follow

dc-down: ## Stop the docker hub
	@$(DOCKER_COMPOSE) down --remove-orphans
.PHONY: dc-down

dc-sh-php: ## Connect to the PHP FPM container
	@$(DOCKER_COMPOSE) exec php sh
.PHONY: dc-sh-php

dc-run-php: ## Launch a new PHP FPM container
	@$(DOCKER_COMPOSE_PROD) run --rm php sh
.PHONY: dc-sh-php