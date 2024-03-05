#!/bin/sh
set -e

echo "
Running with:

* Postgres 13
* PostGIS 3.4
"

docker run -it --rm --network doctrine-postgis-13-34 -e DB_HOST=db-13-34 -v "${PWD}":/app doctrine-postgis-php "$@"
