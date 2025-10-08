#!/bin/sh
set -e

echo "
Running with:

* Postgres 16
* PostGIS 3.4
"

docker run -it --rm --network doctrine-postgis-16-34 -e DB_HOST=db-16-34 -v "${PWD}":/app doctrine-postgis-php "$@"
