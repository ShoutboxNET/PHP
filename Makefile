# Include .env file if it exists
ifneq (,$(wildcard .env))
    include .env
    export
endif

# Check for required environment variables
check-env:
	@if [ -z "$(SHOUTBOX_API_KEY)" ]; then \
		echo "Error: SHOUTBOX_API_KEY is not set"; \
		exit 1; \
	fi
	@if [ -z "$(SHOUTBOX_FROM)" ]; then \
		echo "Error: SHOUTBOX_FROM is not set"; \
		exit 1; \
	fi
	@if [ -z "$(SHOUTBOX_TO)" ]; then \
		echo "Error: SHOUTBOX_TO is not set"; \
		exit 1; \
	fi

# Install dependencies
install:
	composer install

# Update dependencies
update:
	composer update

# Run tests (requires environment variables)
test: check-env
	./vendor/bin/phpunit

# Run direct API example
run-direct-api: check-env
	php examples/direct-api.php

# Run API client example
run-api-client: check-env
	php examples/api-client.php

# Run SMTP example
run-smtp: check-env
	php examples/smtp-client.php

# Run code style checks
cs:
	./vendor/bin/phpcs --standard=PSR12 src/ tests/

# Fix code style issues
cs-fix:
	./vendor/bin/phpcbf --standard=PSR12 src/ tests/

# Clean up
clean:
	rm -rf vendor/
	rm -f composer.lock

# Create .env template file
env-template:
	@echo "SHOUTBOX_API_KEY=" > .env.template
	@echo "SHOUTBOX_FROM=" >> .env.template
	@echo "SHOUTBOX_TO=" >> .env.template
	@echo "Created .env.template file"

# Show help
help:
	@echo "Available commands:"
	@echo "  make install      - Install dependencies"
	@echo "  make update       - Update dependencies"
	@echo "  make test         - Run tests (requires env vars)"
	@echo "  make run-direct-api - Run direct API example"
	@echo "  make run-api-client - Run API client example"
	@echo "  make run-smtp     - Run SMTP example"
	@echo "  make cs           - Run code style checks"
	@echo "  make cs-fix       - Fix code style issues"
	@echo "  make clean        - Clean build artifacts"
	@echo "  make env-template - Create .env.template file"
	@echo ""
	@echo "Required environment variables (can be set in .env file):"
	@echo "  SHOUTBOX_API_KEY - Your Shoutbox API key"
	@echo "  SHOUTBOX_FROM    - Sender email address"
	@echo "  SHOUTBOX_TO      - Recipient email address"

.PHONY: check-env install update test run-direct-api run-api-client run-smtp cs cs-fix clean env-template help
