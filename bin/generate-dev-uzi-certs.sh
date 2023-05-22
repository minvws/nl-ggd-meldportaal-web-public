#!/usr/bin/env bash

# ==============================================================================
# Copyright (c) 2021 De Staat der Nederlanden, Ministerie van Volksgezondheid, Welzijn en Sport.
#
# Licensed under the EUROPEAN UNION PUBLIC LICENCE v. 1.2&
#
# SPDX-License-Identifier: EUPL-1.2
# ==============================================================================

set -o errexit -o errtrace -o nounset -o pipefail

# Generate development UZI certificates for connecting to the API
generate_uzi_certs() {
  local path

  readonly path="${1?Parameters required: <ssl-directory>}"

  if [ ! -f "${path}/uzi-server-cert.crt" ]; then
    echo "Generating UZI server certificate for API"
    openssl req -x509 \
      -nodes \
      -keyout ${path}/uzi-server-cert.key \
      -out ${path}/uzi-server-cert.crt \
      -days 3650 \
      -subj "/C=NL/O=uzi-dev/CN=client/serialNumber=1234ABCD" \
      -addext "subjectAltName = otherName:2.5.5.5;IA5STRING:2.16.528.1.1003.1.3.5.5.2-1-12345678-S-90000123-00.000-00000000"

    openssl pkcs12 -export -in ${path}/uzi-server-cert.crt -inkey ${path}/uzi-server-cert.key -out ${path}/uzi-server-cert.p12 -passout pass:
  fi
}


if [ "${BASH_SOURCE[0]}" == "${0}" ]; then
    generate_uzi_certs "${@}"
else
    export -f generate_uzi_certs
fi
