#!/bin/sh
set -e

echo "
Running with:

* Postgres 18
* PostGIS 3.6
"

docker run -it --rm --network doctrine-postgis-18-36 -e DB_HOST=db-18-36 -v "${PWD}":/app doctrine-postgis-php "$@"
