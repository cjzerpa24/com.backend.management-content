# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up -d --build

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

bash: ## Connect to the PHP container
	@$(PHP_CONT) bash

ps: ## list local docker containers
	@$(DOCKER_COMP) ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}"

## —— Database  ————————————————————————————————————————————————————————————————
db-create: ## Create database if not exists
	@$(SYMFONY) --env=dev doctrine:database:create --if-not-exists

db-drop: ## Delete database
	@$(SYMFONY) --env=dev doctrine:database:drop --force

db-migration-gen: ## generate migration from orm setting
	@$(SYMFONY) --env=dev doctrine:migrations:diff --formatted

db-migration-up: ## Execute migrations
	@$(SYMFONY) --env=dev doctrine:migrations:migrate --no-interaction

## —— This tasks run inside container ————————————————————————————————————————————————————————————————
## —— Composer  ————————————————————————————————————————————————————————————————
.PHONY: composer
composer: ## Run composer install
	composer install --no-progress --no-ansi --no-interaction --prefer-dist

.PHONY: cc
cc: ## Remove cache
	rm -rf var/cache/*

.PHONY: clear
clear: cc ## Clear var folder
	rm -rf var/build/*
	rm -rf var/log/*
