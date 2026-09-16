URL ?= http://recompany.test

.PHONY: help serve test

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-8s %s\n", $$1, $$2}'

serve: ## Open the game served by Herd
	open $(URL)

test: ## Run unit tests
	node --test "tests/**/*.test.js"
