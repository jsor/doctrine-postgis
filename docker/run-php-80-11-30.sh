#!/bin/sh
set -e

echo "
Running with:

* PHP 8.0
* Postgres 11
* PostGIS 3.0
"

docker run -it --rm --network postgis-11-30 -e DB_HOST=db-11-30 -v "$(PWD)":/app doctrine-postgis-php80 "$@"
