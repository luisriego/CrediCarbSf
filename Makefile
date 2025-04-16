
#!/bin/bash

UID = $(shell id -u)
DOCKER_BE = cch-app

.PHONY: help start stop restart build prepare run logs composer-install ssh db-create fixtures-load setup-db

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

start: ## Start the containers
	docker network create cch-network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker-compose up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose stop

restart: ## Restart the containers
	$(MAKE) stop && $(MAKE) start

build: ## Rebuilds all the containers
	docker network create cch-network || true
	cp -n docker-compose.yml.dist docker-compose.yml || true
	U_ID=${UID} docker-compose build

prepare: ## Runs backend commands
	$(MAKE) composer-install
	$(MAKE) setup-db

run: ## starts the Symfony development server in detached mode
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} symfony serve -d

logs: ## Show Symfony logs in real time
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} symfony server:log

# Backend commands
composer-install: ## Installs composer dependencies
	U_ID=${UID} docker exec --user ${UID} ${DOCKER_BE} composer install --no-interaction
# End backend commands

ssh: ## bash into the be container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash

ssh-sudo:
	U_ID=${UID} docker exec -it --user root ${DOCKER_BE} bash

code-style-check:
  U_ID=${UID} docker exec --user ${UID} ${DOCKER_BE} vendor/bin/php-cs-fixer fix src --rules=@Symfony --dry-run

db-create: ## Create the database
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:database:create --if-not-exists
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:database:create --if-not-exists --env=test 

	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:schema:update --force
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:schema:update --force --env=test

test-db-create: ## Create the database for testing
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:database:create --if-not-exists --env=test 
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console doctrine:schema:update --force --env=test

fixtures-load: ## Load fixtures
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php bin/console hautelook:fixtures:load --no-interaction

setup-db: ## Create the database and load fixtures
	$(MAKE) db-create && $(MAKE) fixtures-load

test-setup-db: ## Create the database and load fixtures for testing
	$(MAKE) test-db-create && $(MAKE) fixtures-load

.PHONY: tests
tests:
	U_ID=${UID} docker exec --user ${UID} ${DOCKER_BE} vendor/bin/phpunit -c phpunit.xml.dist
