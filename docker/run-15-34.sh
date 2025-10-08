#!/bin/sh
set -e

echo "
Running with:

* Postgres 15
* PostGIS 3.4
"

docker run -it --rm --network doctrine-postgis-15-34 -e DB_HOST=db-15-34 -v "${PWD}":/app doctrine-postgis-php "$@"
