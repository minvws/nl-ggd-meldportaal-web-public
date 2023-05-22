#!/usr/bin/env bash

export TERM="${TERM:-xterm-256color}"

set -o errexit -o errtrace -o nounset -o pipefail

check_development_environment() {
    local bRestoreX

    iErrors=0
    iWarnings=0

    # Avoid `tput` to mess up output in debug mode (bash -x)
    if [[ "$-" == *x* ]]; then bRestoreX=true; set +x; fi
    : readonly "${BACKGROUND_BLUE:=$(tput setab 4)}"
    : readonly "${BACKGROUND_GREEN:=$(tput setab 2)}"
    : readonly "${BACKGROUND_RED:=$(tput setab 1)}"
    : readonly "${BACKGROUND_YELLOW:=$(tput setab 3)}"
    : readonly "${COLOR_WHITE:=$(tput setaf 7)}"
    : readonly "${RESET_TEXT:=$(tput sgr0)}"      # turn off all attributes
    : readonly "${TEXT_DIM:=$(tput dim)}"         # turn on half-bright mode
    if [[ "${bRestoreX:=false}" == true ]]; then bRestoreX=false; set -x;fi

    error() {
        echo -e "${BACKGROUND_RED}${COLOR_WHITE} ERROR ${RESET_TEXT} $*"
        ((iErrors++))
    }

    exit_trap() {
        {
            [ "${iWarnings}" -gt 0 ]  && warn "${iWarnings} checks issued warnings"
        } || {
            [ "${iErrors}" -gt 0 ] && { error "${iErrors} error occurred" && exit 1; }
        } || {
            success 'All checks passed'
        }
    }

    info() {
        echo -e "${BACKGROUND_BLUE} INFO ${RESET_TEXT} $*"
    }

    success() {
        echo -e "${BACKGROUND_GREEN} SUCCESS ${RESET_TEXT} $*"
    }

    warn() {
        echo -e "${BACKGROUND_YELLOW}${COLOR_WHITE} WARNING ${RESET_TEXT} $*"
        ((iWarnings++))
    }

    trap 'exit_trap' EXIT

    # Warn if the UZI certificate is in override mode, as this can cause trouble when testing non-logged-in users (Or running feature tests)
    [ "${OVERRIDE_UZI_CERT:-}" = '' ] || warn 'The environment variable OVERRIDE_UZI_CERT has been set. This means changes from .env will not be picked up!'

    # Make sure composer has run
    [ -d vendor ]  || error "The vendor/ directory is missing ${TEXT_DIM}(Try running 'composer install')${RESET_TEXT}"

    # Make sure development environment variables have been provided
    [ -f .env ] || error 'Required file missing: .env'

    # Make sure certificates for Apache SSLCertificateFile SSLCertificateKeyFile config are present
    { [ -f ssl/meldportaal.localdev.crt ] && [ -f ssl/meldportaal.localdev.key ]; } || error "Certificates are missing ${TEXT_DIM}(Try generating them using openssl)${RESET_TEXT}"

    # Make sure APP_KEY is set
    [ "$(php -r "require __DIR__.'/public/index.php';" 2>&1 | grep 'MissingAppKeyException' -c)" -eq 0 ] || {
        error "Application key not set ${TEXT_DIM}(Try running 'php artisan key:generate')${RESET_TEXT}"
        exit
    }

    # Make sure database migrations are possible and warn if they are not up to date
    php artisan migrate:status --quiet || error "Database migration has not run ${TEXT_DIM}(Try running 'php artisan migrate')${RESET_TEXT}"
    [ "$(php artisan migrate:status --no-ansi | grep -E '\bNo\b' --count )" = 0 ] || warn 'Database migrations not up to date.'

    # ...and we're done!
    echo "$HOSTNAME is running on https://$(grep "$HOSTNAME" /etc/hosts | cut -f1)"
}

check_development_environment "$@"
