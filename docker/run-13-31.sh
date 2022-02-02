#!/bin/sh
set -e

echo "
Running with:

* Postgres 13
* PostGIS 3.1
"

docker run -it --rm --network doctrine-postgis-13-31 -e DB_HOST=db-13-31 -v "$(PWD)":/app doctrine-postgis-php "$@"
