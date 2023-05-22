#!/usr/bin/env bash

# ==============================================================================
# Copyright (c) 2021 De Staat der Nederlanden, Ministerie van Volksgezondheid, Welzijn en Sport.
#
# Licensed under the EUROPEAN UNION PUBLIC LICENCE v. 1.2&
#
# SPDX-License-Identifier: EUPL-1.2
# ==============================================================================

set -o errexit -o errtrace -o nounset -o pipefail

# Generate TLS certificate for running this application on HTTPS
generate_crypto() {
  local name path

  readonly name="${1?Two parameters required: <server-name> <ssl-directory>}"
  readonly path="${2?Two parameters required: <server-name> <ssl-directory>}"

  mkdir -p "${path}"

  openssl req \
    -days 3650 \
    -keyout "${path}/${name}.key" \
    -newkey rsa:2048 \
    -nodes \
    -out "${path}/${name}.crt" \
    -sha256 \
    -subj "/CN=${name}/C=NL/L=Amsterdam" \
    -x509
}

if [ "${BASH_SOURCE[0]}" == "${0}" ]; then
    generate_crypto "${@}"
else
    export -f generate_crypto
fi
