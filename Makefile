SHELL := /bin/bash
PWD = $(shell pwd)

.PHONY: build help test

default: help

help: ## The help text you're reading
	@grep --no-filename -E '^[a-zA-Z1-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build-php70: ## Build the PHP 7.0 container
	docker build . -f docker/7.0/Dockerfile -t noiselabs/smarty-bundle:latest-php7.0

build-php71: ## Build the PHP 7.1 container
	docker build . -f docker/7.1/Dockerfile -t noiselabs/smarty-bundle:latest-php7.1

build-php72: ## Build the PHP 7.2 container
	docker build . -f docker/7.2/Dockerfile -t noiselabs/smarty-bundle:latest-php7.2

build-php73: ## Build the PHP 7.3 container
	docker build . -f docker/7.3/Dockerfile -t noiselabs/smarty-bundle:latest-php7.3

build: ## Build Docker containers
	$(MAKE) build-php70 build-php71 build-php72 build-php73

build-parallel: ## Build Docker containers in parallel
	$(MAKE) -j4 build-php70 build-php71 build-php72 build-php73

test-php70: ## Run unit and functional tests in the PHP 7.0 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.0 composer test

test-php71: ## Run unit and functional tests in the PHP 7.1 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.1 composer test

test-php72: ## Run unit and functional tests in the PHP 7.2 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.2 composer test

test-php73: ## Run unit and functional tests in the PHP 7.3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.3 composer test

test: ## Run unit and functional tests
	$(MAKE) test-php70 test-php71 test-php72 test-php73

test-parallel: ## Run unit and functional tests in parallel
	$(MAKE) -j4 test-php70 test-php71 test-php72 test-php73

sh-php70: ## Get a shell in the PHP 7.0 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.0 sh

sh-php71: ## Get a shell in the PHP 7.1 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.1 sh

sh-php72: ## Get a shell in the PHP 7.2 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.2 sh

sh-php73: ## Get a shell in the PHP 7.3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor noiselabs/smarty-bundle:latest-php7.3 sh

web-php70: ## Launches the built-in PHP web server in the PHP 7.0 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9080:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.0 php -S 0.0.0.0:8080 -t web

web-php71: ## Launches the built-in PHP web server in the PHP 7.1 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9081:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.1 php -S 0.0.0.0:8080 -t web

web-php72: ## Launches the built-in PHP web server in the PHP 7.2 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9082:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.2 php -S 0.0.0.0:8080 -t web

web-php73: ## Launches the built-in PHP web server in the PHP 7.3 container
	docker run --rm -it --mount type=bind,src=$(PWD),dst=/app --mount type=volume,dst=/app/vendor -p 127.0.0.1:9083:8080/tcp -w /app/Tests/Functional/App noiselabs/smarty-bundle:latest-php7.3 php -S 0.0.0.0:8080 -t web
