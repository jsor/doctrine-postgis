#!/bin/sh
set -e

echo "
Running with:

* Postgres 17
* PostGIS 3.6
"

docker run -it --rm --network doctrine-postgis-17-36 -e DB_HOST=db-17-36 -v "${PWD}":/app doctrine-postgis-php "$@"
