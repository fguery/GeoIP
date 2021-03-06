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

install-tests: composer-update-test docker-build

run-test:
	docker exec -e tableName="geo_ip_test" geoip_php vendor/bin/phpunit

import-db:
	docker exec geoip_php bin/console.php import

docker-build:
	docker-compose build

start:
	docker-compose up
stop:
	docker-compose down
