.PHONY: help

# User Id
UNAME = $(shell uname)

ifeq ($(UNAME), Linux)
   UID = $(shell id -u)
else
   UID = 1000
endif

## Display this help text
help:
	$(info ---------------------------------------------------------------------)
	$(info -                        Available targets                          -)
	$(info ---------------------------------------------------------------------)
	@awk '/^[a-zA-Z\-\_0-9]+:/ {                                   \
	nb = sub( /^## /, "", helpMsg );                             \
	if(nb == 0) {                                                \
		helpMsg = $$0;                                             \
		nb = sub( /^[^:]*:.* ## /, "", helpMsg );                  \
	}                                                            \
	if (nb)                                                      \
		printf "\033[1;31m%-" width "s\033[0m %s\n", $$1, helpMsg; \
	}                                                              \
	{ helpMsg = $$0 }'                                             \
	$(MAKEFILE_LIST) | column -ts:

## Pull images used in docker-compose config
pull:
	docker-compose pull

## Build images from docker-compose config
build: pull
	mkdir ~/dolibarr_documents
	docker-compose build

## Start all the containers
up:
	docker-compose up -d
## Alias -> up
start: up

## Stop all the containers
stop:
	docker-compose stop

## Stop, then... start
restart: stop start

## Remove all the containers (unmapped data will be lost)
remove: stop
	docker-compose rm -f

## Down all the containers
down:
	docker-compose down

## Enter interactive shell into php container
php:
	docker-compose exec --user www-data php bash

## Alias -> internal_update
update:  internal_update

## Symfony cache clear
cc:
	docker-compose exec --user www-data php ./bin/console cache:clear

## composer + upgrade (migrations,...)
internal_update:
	docker-compose exec --user www-data php composer install
	docker-compose exec --user www-data php ./scripts/deploy/upgrade.sh

## Upgrade sources + rebuild container + launch migrations
upgrade:  build up internal_update encore

## yarn install
yarn:
	docker run -it --rm -v $(CURDIR):/app -w /app --user $(UID) node:8.11.3-alpine yarn install

## Update front dependencies
encore: yarn
	docker run -it --rm -v $(CURDIR):/app -w /app --user $(UID) node:8.11.3-alpine ./node_modules/.bin/encore dev

## Watch webpack modifications
watch: yarn
	docker run -d --rm --name encore_watch -v $(CURDIR):/app -w /app --user $(UID) node:8.11.3-alpine ./node_modules/.bin/encore dev --watch

## Stop webpack watcher
watch_stop:
	docker stop encore_watch

### reserved WIP for tests
## Run the stack for tests
run-test:
	docker-compose -f docker-compose-test.yml build
	docker-compose -f docker-compose-test.yml up -d
	docker-compose -f docker-compose-test.yml exec --user www-data test_php sh run-phpunit.sh

## Run tests
test:
	docker-compose -f docker-compose-test.yml exec --user www-data test_php sh run-phpunit.sh

## Remove test stack
down-test:
	docker-compose -f docker-compose-test.yml down

## Logs for all containers of the project
logs:
	docker-compose logs -f
## init db
init-db:init-documents
	mysql -uroot -h mysql-db.localhost -p"test" -e "DROP DATABASE IF EXISTS dolibarr;"
	mysql -uroot -h mysql-db.localhost -p"test" -e "CREATE DATABASE IF NOT EXISTS dolibarr DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
	mysql -uroot -h mysql-db.localhost -p"test" -o dolibarr < docker/dolibarr.sql

init-documents:
	mkdir -p ~/dolibarr_documents/users/temps
	touch ~/dolibarr_documents/install.lock
