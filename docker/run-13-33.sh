#!/bin/sh
set -e

echo "
Running with:

* Postgres 13
* PostGIS 3.3
"

docker run -it --rm --network doctrine-postgis-13-33 -e DB_HOST=db-13-33 -v "${PWD}":/app doctrine-postgis-php "$@"
