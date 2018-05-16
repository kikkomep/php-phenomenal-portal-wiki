#!/usr/bin/env bash

# configuration
path="/var/www/html/php-phenomenal-portal-wiki"
htmlFolder="$path/wiki-html"
markdownFolder="$path/wiki-markdown"
gitList="$path/conf/gitList.txt"
gitBranch="master"

# path of this script
current_path="$( cd "$(dirname "${0}")" ; pwd -P )"

# launch converter
"${current_path}/markdown2html/run.sh" \
    --force-cleanup \
    --html "${htmlFolder}" \
    --md "${markdownFolder}" \
    --git-branch "${gitBranch}" \
    "${gitList}"