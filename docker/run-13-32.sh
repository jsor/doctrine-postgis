#!/bin/sh
set -e

echo "
Running with:

* Postgres 13
* PostGIS 3.2
"

docker run -it --rm --network doctrine-postgis-13-32 -e DB_HOST=db-13-32 -v "${PWD}":/app doctrine-postgis-php "$@"
