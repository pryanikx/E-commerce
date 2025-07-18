DOCKER_COMPOSE = docker compose
PHP_SERVICE = ecommerce_php
FRONTEND_SERVICE = ecommerce_front
NGINX_SERVICE = ecommerce_nginx
MYSQL_SERVICE = ecommerce_mysql
RABBITMQ_SERVICE = ecommerce_rabbitmq
LOCALSTACK_SERVICE = ecommerce_localstack
COMPOSER = $(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer
ARTISAN = $(DOCKER_COMPOSE) exec $(PHP_SERVICE) php artisan
NPM = $(DOCKER_COMPOSE) exec $(FRONTEND_SERVICE) npm

.PHONY: all
all: build up

.PHONY: build
build:
	$(DOCKER_COMPOSE) build

.PHONY: up
up:
	$(DOCKER_COMPOSE) up -d

.PHONY: down
down:
	$(DOCKER_COMPOSE) down

.PHONY: down-v
down-v:
	$(DOCKER_COMPOSE) down -v

.PHONY: rebuild
rebuild: down build up

.PHONY: composer-install
composer-install:
	$(COMPOSER) install

.PHONY: composer-update
composer-update:
	$(COMPOSER) update

.PHONY: migrate
migrate:
	$(ARTISAN) migrate

.PHONY: migrate-seed
migrate-seed:
	$(ARTISAN) migrate --seed

.PHONY: test
test:
	$(ARTISAN) test

.PHONY: php-shell
php-shell:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) bin/bash

.PHONY: mysql-shell
mysql-shell:
	$(DOCKER_COMPOSE) exec $(MYSQL_SERVICE) mysql -u root -p

.PHONY: npm-install
npm-install:
	$(NPM) install

.PHONY: npm-build
npm-build:
	$(NPM) run build

.PHONY: npm-dev
npm-dev:
	$(NPM) run dev

.PHONY: logs
logs:
	$(DOCKER_COMPOSE) logs -f

.PHONY: logs-php
logs-php:
	$(DOCKER_COMPOSE) logs -f $(PHP_SERVICE)

.PHONY: logs-nginx
logs-nginx:
	$(DOCKER_COMPOSE) logs -f $(NGINX_SERVICE)

.PHONY: logs-frontend
logs-frontend:
	$(DOCKER_COMPOSE) logs -f $(FRONTEND_SERVICE)

.PHONY: logs-mysql
logs-mysql:
	$(DOCKER_COMPOSE) logs -f $(MYSQL_SERVICE)

.PHONY: logs-rabbitmq
logs-rabbitmq:
	$(DOCKER_COMPOSE) logs -f $(RABBITMQ_SERVICE)

.PHONY: logs-localstack
logs-localstack:
	$(DOCKER_COMPOSE) logs -f $(LOCALSTACK_SERVICE)

.PHONY: clean
clean:
	docker system prune -f
	docker volume prune -f

.PHONY: restart-php
restart-php:
	$(DOCKER_COMPOSE) restart $(PHP_SERVICE)

.PHONY: restart-nginx
restart-nginx:
	$(DOCKER_COMPOSE) restart $(NGINX_SERVICE)

.PHONY: restart-frontend
restart-frontend:
	$(DOCKER_COMPOSE) restart $(FRONTEND_SERVICE)

.PHONY: health
health:
	$(DOCKER_COMPOSE) ps

.PHONY: queue-work
queue-work:
	$(ARTISAN) queue:work

.PHONY: clear-cache
clear-cache:
	$(ARTISAN) cache:clear
	$(ARTISAN) config:clear
	$(ARTISAN) route:clear
	$(ARTISAN) view:clear

.PHONY: phpstan
phpstan:
	./vendor/bin/phpstan analyse --level=8 --no-progress --memory-limit=2G

.PHONY: php-cs-fixer
php-cs-fixer:
	./vendor/bin/php-cs-fixer fix --dry-run --diff

.PHONY: php-cs-fixer-fix
php-cs-fixer-fix:
	./src/ecommerce/vendor/bin/php-cs-fixer fix

.PHONY: phpstan-and-fix
phpstan-and-fix: phpstan php-cs-fixer-fix