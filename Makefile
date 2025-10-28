# ============================================================================
# WEBGRE3 - Makefile per comandi Docker
# ============================================================================
# Uso: make [comando]
# Esempio: make up, make logs, make shell
# ============================================================================

.PHONY: help build up down restart logs shell mysql clean

# Default target
.DEFAULT_GOAL := help

# Variables
DOCKER_COMPOSE = docker-compose
CONTAINER_APP = webgre3-app
CONTAINER_REDIS = webgre3-redis

# ============================================================================
# HELP
# ============================================================================
help: ## Mostra questo help
	@echo "============================================"
	@echo "WEBGRE3 - Docker Commands"
	@echo "============================================"
	@echo ""
	@echo "Comandi disponibili:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""

# ============================================================================
# BUILD & START
# ============================================================================
build: ## Build dell'immagine Docker
	$(DOCKER_COMPOSE) build --no-cache

up: ## Avvia i container
	$(DOCKER_COMPOSE) up -d

start: up ## Alias di 'up'

down: ## Ferma e rimuove i container
	$(DOCKER_COMPOSE) down

stop: down ## Alias di 'down'

restart: ## Riavvia i container
	$(DOCKER_COMPOSE) restart

rebuild: down build up ## Rebuild completo (down + build + up)

# ============================================================================
# LOGS & DEBUG
# ============================================================================
logs: ## Mostra i logs in real-time
	$(DOCKER_COMPOSE) logs -f

logs-app: ## Logs solo webgre3-app
	$(DOCKER_COMPOSE) logs -f $(CONTAINER_APP)

logs-redis: ## Logs solo redis
	$(DOCKER_COMPOSE) logs -f $(CONTAINER_REDIS)

ps: ## Stato dei container
	$(DOCKER_COMPOSE) ps

top: ## Processi in esecuzione nei container
	docker exec $(CONTAINER_APP) top

# ============================================================================
# SHELL ACCESS
# ============================================================================
shell: ## Entra nella shell del container webgre3-app
	docker exec -it $(CONTAINER_APP) /bin/sh

bash: shell ## Alias di 'shell'

root: ## Entra come root nel container
	docker exec -it -u root $(CONTAINER_APP) /bin/sh

redis-cli: ## Connetti a Redis CLI
	docker exec -it $(CONTAINER_REDIS) redis-cli -a $$(grep REDIS_PASSWORD .env | cut -d '=' -f2)

# ============================================================================
# DATABASE
# ============================================================================
mysql: ## Connetti a MySQL (core-services)
	docker exec -it $(CONTAINER_APP) mysql -h $$(grep DB_HOST .env | cut -d '=' -f2) -u $$(grep DB_USER .env | cut -d '=' -f2) -p

migrate: ## Esegui migrations
	docker exec $(CONTAINER_APP) php artisan migrate

migrate-fresh: ## Recreate database e migrations
	docker exec $(CONTAINER_APP) php artisan migrate:fresh

migrate-rollback: ## Rollback last migration
	docker exec $(CONTAINER_APP) php artisan migrate:rollback

migrate-status: ## Status migrations
	docker exec $(CONTAINER_APP) php artisan migrate:status

db-seed: ## Seed database
	docker exec $(CONTAINER_APP) php artisan db:seed

db-backup: ## Backup database
	@echo "Creating backup..."
	@mkdir -p backups
	docker exec $(CONTAINER_APP) mysqldump -h $$(grep DB_HOST .env | cut -d '=' -f2) -u $$(grep DB_USER .env | cut -d '=' -f2) -p$$(grep DB_PASS .env | cut -d '=' -f2) $$(grep DB_NAME .env | cut -d '=' -f2) > backups/backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo "✓ Backup saved to backups/"

# ============================================================================
# MAINTENANCE
# ============================================================================
clear-cache: ## Pulisci cache applicazione
	docker exec $(CONTAINER_APP) rm -rf storage/cache/*
	@echo "✓ Cache cleared"

clear-logs: ## Pulisci logs
	docker exec $(CONTAINER_APP) find storage/logs -name "*.log" -type f -delete
	@echo "✓ Logs cleared"

opcache-reset: ## Reset Opcache
	docker exec $(CONTAINER_APP) killall -USR2 php-fpm
	@echo "✓ Opcache reset"

nginx-reload: ## Ricarica Nginx
	docker exec $(CONTAINER_APP) nginx -s reload
	@echo "✓ Nginx reloaded"

supervisor-status: ## Status supervisor
	docker exec $(CONTAINER_APP) supervisorctl status

supervisor-restart: ## Restart tutti i processi supervisor
	docker exec $(CONTAINER_APP) supervisorctl restart all
	@echo "✓ All processes restarted"

# ============================================================================
# COMPOSER & DEPENDENCIES
# ============================================================================
composer-install: ## Install Composer dependencies
	docker exec $(CONTAINER_APP) composer install --optimize-autoloader --classmap-authoritative

composer-update: ## Update Composer dependencies
	docker exec $(CONTAINER_APP) composer update

composer-dump: ## Dump autoload
	docker exec $(CONTAINER_APP) composer dump-autoload --optimize --classmap-authoritative

# ============================================================================
# TESTS
# ============================================================================
test: ## Esegui test PHP
	docker exec $(CONTAINER_APP) php vendor/bin/phpunit

test-coverage: ## Test con code coverage
	docker exec $(CONTAINER_APP) php vendor/bin/phpunit --coverage-html coverage

phpcs: ## PHP CodeSniffer
	docker exec $(CONTAINER_APP) vendor/bin/phpcs

phpstan: ## PHPStan static analysis
	docker exec $(CONTAINER_APP) vendor/bin/phpstan analyse

# ============================================================================
# HEALTH & MONITORING
# ============================================================================
health: ## Check application health
	@curl -s http://localhost:$$(grep WEBGRE3_PORT .env | cut -d '=' -f2)/health
	@echo ""

status-fpm: ## PHP-FPM status
	@curl -s http://localhost:$$(grep WEBGRE3_PORT .env | cut -d '=' -f2)/status
	@echo ""

ping: ## Ping PHP-FPM
	@curl -s http://localhost:$$(grep WEBGRE3_PORT .env | cut -d '=' -f2)/ping
	@echo ""

info: ## Container info
	@echo "============================================"
	@echo "WEBGRE3 Container Info"
	@echo "============================================"
	@echo "Status:    $$(docker inspect -f '{{.State.Status}}' $(CONTAINER_APP))"
	@echo "Uptime:    $$(docker inspect -f '{{.State.StartedAt}}' $(CONTAINER_APP))"
	@echo "Image:     $$(docker inspect -f '{{.Config.Image}}' $(CONTAINER_APP))"
	@echo "Network:   $$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.NetworkID}}{{end}}' $(CONTAINER_APP))"
	@echo "Port:      $$(grep WEBGRE3_PORT .env | cut -d '=' -f2)"
	@echo "============================================"

# ============================================================================
# CLEANUP
# ============================================================================
clean: ## Rimuovi container, volumi e network
	$(DOCKER_COMPOSE) down -v
	@echo "✓ Containers, volumes, and networks removed"

clean-all: clean ## Rimuovi tutto incluse immagini
	docker rmi webgre3-app || true
	@echo "✓ Images removed"

prune: ## Pulisci Docker (attenzione!)
	docker system prune -f
	@echo "✓ Docker system pruned"

# ============================================================================
# DEVELOPMENT
# ============================================================================
dev: ## Avvia in modalità development
	@echo "Starting in DEVELOPMENT mode..."
	@sed -i 's/APP_ENV=production/APP_ENV=development/' .env
	@sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
	@$(MAKE) restart
	@echo "✓ Development mode enabled"

prod: ## Avvia in modalità production
	@echo "Starting in PRODUCTION mode..."
	@sed -i 's/APP_ENV=development/APP_ENV=production/' .env
	@sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
	@$(MAKE) restart
	@echo "✓ Production mode enabled"

# ============================================================================
# UTILITY
# ============================================================================
watch-logs: ## Tail logs con grep pattern (uso: make watch-logs PATTERN=error)
	$(DOCKER_COMPOSE) logs -f | grep -i "$(PATTERN)"

env: ## Mostra variabili ambiente
	docker exec $(CONTAINER_APP) env | sort

ports: ## Mostra porte in uso
	docker port $(CONTAINER_APP)

size: ## Dimensione immagine
	docker images webgre3-app --format "table {{.Repository}}\t{{.Size}}"

inspect: ## Inspect container
	docker inspect $(CONTAINER_APP)

# ============================================================================
# NETWORK
# ============================================================================
network-ls: ## Lista network Docker
	docker network ls

network-inspect: ## Ispeziona network webgre3
	docker network inspect webgre3-network

network-core: ## Ispeziona network core-services
	docker network inspect core-services_default

# ============================================================================
# BACKUP & RESTORE
# ============================================================================
backup-volumes: ## Backup di tutti i volumi
	@echo "Backing up volumes..."
	@mkdir -p backups/volumes
	docker run --rm -v webgre3-storage:/data -v $$(pwd)/backups/volumes:/backup alpine tar czf /backup/storage_$$(date +%Y%m%d_%H%M%S).tar.gz /data
	docker run --rm -v webgre3-uploads:/data -v $$(pwd)/backups/volumes:/backup alpine tar czf /backup/uploads_$$(date +%Y%m%d_%H%M%S).tar.gz /data
	@echo "✓ Volumes backed up to backups/volumes/"

# ============================================================================
# DOCUMENTATION
# ============================================================================
readme: ## Apri documentazione
	@cat DOCKER_SETUP.md

quick-start: ## Mostra quick start guide
	@head -n 100 DOCKER_SETUP.md
