#!/bin/sh
set -e

echo "
Running with:

* Postgres 11
* PostGIS 3.0
"

docker run -it --rm --network doctrine-postgis-11-30 -e DB_HOST=db-11-30 -v "$(PWD)":/app doctrine-postgis-php "$@"
