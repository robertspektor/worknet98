.DEFAULT_GOAL := help

.PHONY: help setup dev build format lint phpstan test test-php test-js all migrate fresh

help: ## Show available targets
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

setup: ## Install dependencies, migrate and build assets
	composer install
	npm install
	php artisan migrate --force
	npm run build

dev: ## Start the Vite dev server (port 5190) and the scheduler
	npx concurrently --names vite,schedule "npm run dev" "php artisan schedule:work"

build: ## Build frontend assets
	npm run build

format: ## Format PHP (Pint) and frontend (Vite+)
	./vendor/bin/pint --parallel
	npm run check:fix

lint: ## Check formatting, lint and types
	./vendor/bin/pint --parallel --test
	npm run check
	npm run types:check

phpstan: ## Run static analysis
	./vendor/bin/phpstan analyse --memory-limit=1G

test: test-php test-js ## Run all test suites

test-php: ## Run the Pest suite
	php artisan test

test-js: ## Run the frontend unit tests
	npm run test

all: format lint phpstan test ## Format, lint, analyse and test

migrate: ## Run pending migrations
	php artisan migrate

fresh: ## Drop and rebuild the local database with seed data
	php artisan migrate:fresh --seed
