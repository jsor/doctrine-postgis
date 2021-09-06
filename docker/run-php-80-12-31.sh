#!/bin/sh
set -e

echo "
Running with:

* PHP 8.0
* Postgres 12
* PostGIS 3.1
"

docker run -it --rm --network postgis-12-31 -e DB_HOST=db-12-31 -v "$(PWD)":/app doctrine-postgis-php80 "$@"
