default: install

composer-install:
	docker run --rm --interactive --tty \
		--volume $(shell pwd):/app \
		composer install --ignore-platform-reqs --no-scripts --no-dev --optimize-autoloader

composer-update:
	docker run --rm --interactive --tty \
		--volume $(shell pwd):/app \
	composer update --ignore-platform-reqs --no-scripts --no-dev --optimize-autoloader

composer-update-test:
	docker run --rm --interactive --tty \
		--volume $(shell pwd):/app \
	composer update --ignore-platform-reqs


install: composer-install docker-build

test: composer-update-test docker-build run-test

run-test:
	docker run --rm --interactive --tty \
		--volume $(shell pwd):/var/www/ \
		--name geoip_test \
		--env dbName='geoIP_test' \
		-w /var/www/ \
		geoip_slim:latest vendor/bin/phpunit

docker-build:
	docker-compose build

start:
	docker-compose up
stop:
	docker-compose down
