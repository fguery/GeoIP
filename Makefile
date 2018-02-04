default: install

composer-install:
	docker run --rm --interactive --tty \
		--volume $(shell pwd):/app \
		composer install --ignore-platform-reqs --no-scripts

composer-update:
	docker run --rm --interactive --tty \
		--volume $(shell pwd):/app \
	composer update --ignore-platform-reqs --no-scripts

install: composer-install docker-build

docker-build:
	docker-compose build

start:
	docker-compose up
