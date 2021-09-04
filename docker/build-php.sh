#!/bin/sh
set -e

docker build -t doctrine-postgis-php80 --target php80 ./docker/
