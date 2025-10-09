#!/bin/sh
set -e

echo "
Running with:

* Postgres 14
* PostGIS 3.2
"

docker run -it --rm --network doctrine-postgis-14-32 -e DB_HOST=db-14-32 -v "${PWD}":/app doctrine-postgis-php "$@"
