#!/bin/sh
set -e

echo "
Running with:

* PHP 8.0
* Postgres 13
* PostGIS 3.0
"

docker run -it --rm --network postgis-13-30 -e DB_HOST=db-13-30 -v "$(PWD)":/app doctrine-postgis-php80 "$@"
