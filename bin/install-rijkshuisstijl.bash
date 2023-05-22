#!/usr/bin/env bash

# ==============================================================================
# Copyright (c) 2021 De Staat der Nederlanden, Ministerie van Volksgezondheid, Welzijn en Sport.
#
# Licensed under the EUROPEAN UNION PUBLIC LICENCE v. 1.2&
#
# SPDX-License-Identifier: EUPL-1.2
# ==============================================================================

set -o errexit -o errtrace -o nounset -o pipefail

install_rijkshuisstijl() {
    local sColor sProjectRoot sSourcePath sTargetPath sRepoPath

    # Available colors: brown dark-blue dark-brown dark-green dark-yellow green
    #                   light-blue mint-green moss-green orange pink purple red
    #                   sky-blue violet yellow
    readonly sColor='ruby-red'


    readonly sProjectRoot="$(realpath "$(dirname "$(dirname "${BASH_SOURCE[0]}")")")"
    readonly sRepoPath="$(realpath "${sProjectRoot}/../manon")"
    # Somehow mounting from a /tmp/tmp.XXX folder does not work in Docker.
    # Use hard-coded path for now, rather than mktemp
    # @TODO: Use tmp dir.
    # readonly sRepoPath="$(mktemp --directory)"
    # trap "rm  --verbose --force --recursive ${sRepoPath}" EXIT

    readonly sSourcePath="${sRepoPath}/static"
    readonly sTargetPath="${sProjectRoot}/public/huisstijl"

    info(){
        echo -e " =====> ${*}"
    }

    error(){
        echo -e " Error! ${*}" >> /dev/stderr
        exit 1
    }

    getSourceCode() {
        info 'Getting the source code'
        if [[ ! -d "${sRepoPath}" ]];then
            git clone git@github.com:91divoc-ln/manon.git "${sRepoPath}"
        elif [[ -d "${sRepoPath}/.git" ]];then
            git -C "${sRepoPath}" pull --ff-only
        else
            error "Could not find git checkout of Manon at ${sRepoPath}"
        fi
    }

    moveFiles() {
        info 'Moving generated files'
        # Make sure target directories exists
        mkdir --parents \
            "${sProjectRoot}/public/js" \
            "${sTargetPath}/css" \
            "${sTargetPath}/fonts" \
            "${sTargetPath}/img"

        # move the files we want
        mv --update \
            "${sSourcePath}"/fonts/* \
            "${sTargetPath}/fonts"

        mv --update \
            "${sSourcePath}"/js/* \
            "${sProjectRoot}/public/js"

        mv --update \
            "${sSourcePath}/css/manon-${sColor}.css" \
            "${sTargetPath}/css"

        mv --update \
            "${sSourcePath}/img/notification_icons.svg" \
            "${sSourcePath}/img/ro-logo-full.svg" \
            "${sSourcePath}/img/ro-logo.svg" \
            "${sTargetPath}/img"

        if [[ -d "${sRepoPath}/.git" ]];then
            info 'Restoring missing file'
            git -C "${sRepoPath}" restore .
        fi
    }

    runBuild() {
        info 'Running npm build'
        docker run \
            -it \
            -v "${sRepoPath}:/usr/src/app" \
            -w /usr/src/app \
            --name npm-rijkshuistijl \
            --rm \
            --user "$(id -u)" \
            'node' \
            npm run build
    }

    getSourceCode
    runBuild
    moveFiles

    echo -e "\nUpdated Manon to version: $(git -C "${sRepoPath}" describe --tags)"
}

if [ "${BASH_SOURCE[0]}" == "${0}" ]; then
    install_rijkshuisstijl "${@}"
else
    export -f install_rijkshuisstijl
fi
