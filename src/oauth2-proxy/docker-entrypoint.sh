#!/bin/bash
set -e

if [ "${1:0:1}" = '-' ]; then
	set -- oauth2_proxy "$@"
fi

if [ "$1" = 'oauth2_proxy' ]; then
	exec su-exec oauth2_proxy "$@"
fi

exec "$@"
