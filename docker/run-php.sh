#!/bin/sh
set -e

docker run -it --rm -v "${PWD}":/app doctrine-postgis-php "$@"
