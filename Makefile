# Makefile
#
# This file contains the commands most used in DEV.
#
# The commands are to be organized semantically and alphabetically.
# The goal is that similar commands are next to each other and we can compare them and update them easily.
#
# For example in a format like `subject-action-environment`, ie:
#
#   test:               # Clear cache, run static analysis and tests.a

# Suppresses Make-specific output. Remove for more debugging info.
.SILENT:

# Make commands be run with `bash` instead of the default `sh`
#SHELL='/usr/bin/env bash'

all: help

install-dev: ## Install development settings for local development
	composer install
	php artisan ide-helper:generate
	mkdir -p env
	php artisan key:generate
	#TO DO: create database
	php artisan migrate
	#TO DO: seed with some fixture data maybe

clean: ## Clean local data
	@echo -n "This will clear your local data. Are you sure you want to do this? [y/N] " && read ans && [ $${ans:-N} = y ]
	php artisan migrate:fresh --seed

start-dev:  ## Start local development setup
	echo "Installing dependencies"
	composer install
	npm i
	npm run build
	${MAKE} serve-dev

serve-dev:  ## Start the local development server
	php artisan serve

test: ## Runs tests
	echo "Clearing cache and running tests."
	php artisan route:clear && php artisan config:clear
	php artisan ide-helper:generate
	php artisan security-check:now
	vendor/bin/phpstan analyse
	vendor/bin/phpcs
	npm run lint
	npm run test
	vendor/bin/psalm

test-php: ## Test PHP
	echo "Clearing cache and running tests"
	php artisan route:clear && php artisan config:clear
	php artisan ide-helper:generate
	php artisan security-check:now
	vendor/bin/phpstan analyse
	vendor/bin/phpcs
	vendor/bin/psalm

test-js: ## Test Javascript/CSS
	npm run lint
	npm run test

install-rf:
	python3 -m venv env
	env/bin/python -m pip install -r tests/robot_framework/requirements.txt --use-deprecated=legacy-resolver
	env/bin/python -m pip install --no-deps robotframework-postgresqldb
	env/bin/rfbrowser init

test-rf: ## Run Robot Framework tests
	env/bin/python -m robot -d tests/robot_framework/results -x outputxunit.xml -v headless:true tests/robot_framework

test-rf/%: ## Run Robot Framework tests with matching tag
	env/bin/python -m robot -d tests/robot_framework/results -x outputxunit.xml -i $* -v headless:true tests/robot_framework

test-rf-head/%: ## Run Robot Framework  with browser visible, with matching tag
	env/bin/python -m robot -d tests/robot_framework/results -x outputxunit.xml -i $* -v headless:false tests/robot_framework

update-db-schema: ## Update database schema
	echo "Downloading database repo"
	git clone git@github.com:minvws/nl-rdo-databases ./database/repo
	echo "Building db schema"
	find ./database/repo/meldportaal_db/v* -type f -name '*.sql' | sort -V | xargs cat > ./database/schema/pgsql-schema.sql
	sed -i -e 's/[ \r\t]*$$//' ./database/schema/pgsql-schema.sql
	echo "Removing downloaded database repo"
	rm -rf ./database/repo
	echo "Done"

init-meldportaal:  ## Create .env and generate certs
	cp .env.example .env
	mkdir -p ssl
	openssl genrsa -out "ssl/privkey.pem" 2048 && openssl req -x509 -new -nodes -key "ssl/privkey.pem" -subj "/CN=test/C=NL/L=Amsterdam" -days 3650 -out "ssl/cert.pem"
	chmod 644 ssl/privkey.pem

install-meldportaal:  ## Install composer and npm dependencies
	composer install
	php artisan key:generate
	npm ci && npm run build

add-user: # Add user
	php artisan user:create 'admin@example.nl' 'Admin user' 'adminpassword1234!' --role super_admin

add-api-user: # Add API user
	php artisan user:create 'api@example.nl' 'API user' 'apiuser1234!' --role api --serial '1234ABCD'

help: ## Display available commands
	echo "Available make commands:"
	echo
	grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
