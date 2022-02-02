#!/bin/sh
set -e

echo "
Running with:

* Postgres 12
* PostGIS 3.0
"

docker run -it --rm --network doctrine-postgis-12-30 -e DB_HOST=db-12-30 -v "$(PWD)":/app doctrine-postgis-php "$@"
