###< manipulations with docker compose app >###
init: up compose migrate

init-from-scratch: remove up-no-cache compose migrate

up:
	docker compose up -d --build

bash:
	docker exec -it gym-assistant-cli bash

# removes containers, their volumes and networks
remove:
	docker compose down -v

# same as remove but also removes downloaded images
purge:
	docker compose down -v --rmi all

up-no-cache:
	docker compose build --no-cache
	docker compose up -d

###< actions with app >###
compose:
	docker compose run --no-deps --rm cli composer install --no-cache --no-progress --no-interaction --no-ansi

# test production behavior in dev environment
compose-no-dev:
	docker compose run --no-deps --rm cli composer install --no-dev --no-cache --no-progress --no-interaction --no-ansi

migrate:
	docker compose run --no-deps --rm cli php bin/console doctrine:database:create --if-not-exists --no-interaction --no-ansi
	docker compose run --no-deps --rm cli php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing

migrate-test:
	docker compose run --no-deps --rm cli php bin/console --env=test doctrine:database:create --if-not-exists --no-interaction --no-ansi
	docker compose run --no-deps --rm cli php bin/console --env=test doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing

clear:
	docker compose run --no-deps --rm cli php bin/console cache:clear
	# delete images created during tests
	docker compose run --no-deps --rm cli rm -f public/img/{exercise,program}/*

###< testing >###
test:
	docker compose run --no-deps --rm cli php bin/phpunit

test-unit:
	docker compose run --no-deps --rm cli php bin/phpunit --testsuite unit

test-integration:
	docker compose run --no-deps --rm cli php bin/phpunit --testsuite integration

test-application:
	docker compose run --no-deps --rm cli php bin/phpunit --testsuite application

test-domain:
	docker compose run --no-deps --rm cli php bin/phpunit --testsuite $(DOMAIN)
	docker compose run --no-deps --rm cli php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --all-or-nothing
