#!/bin/sh
set -e

docker build -t doctrine-postgis-php --target php ./docker/
