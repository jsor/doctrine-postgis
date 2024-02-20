#!/bin/sh
set -e

echo "
Running with:

* Postgres 11
* PostGIS 3.1
"

docker run -it --rm --network doctrine-postgis-11-31 -e DB_HOST=db-11-31 -v "${PWD}":/app doctrine-postgis-php "$@"
