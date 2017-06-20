#!/usr/bin/env bash

set -o nounset

: ${CSFIX_PHP_ARGS:="-vv --diff"}
php-cs-fixer-v2 fix . $CSFIX_PHP_ARGS --config "./.php_cs"
