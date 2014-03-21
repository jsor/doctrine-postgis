#!/bin/bash
# ------------------------------------------------------------------------------
# Adapted from https://github.com/pgRouting/pgrouting/
# ------------------------------------------------------------------------------

DBUSER="postgres"
DBNAME="doctrine_postgis_tests"

POSTGRESQL_VERSION="$1"
POSTGIS_VERSION="$2"

POSTGRESQL_DIRECTORY="/usr/share/postgresql/$POSTGRESQL_VERSION"

# exit script on error
set -e
ERROR=0

# Define alias function for psql command
run_psql () {
    PGOPTIONS='--client-min-messages=warning' psql -X -q -v ON_ERROR_STOP=1 --pset pager=off "$@"
    if [ "$?" -ne 0 ]
    then
    	echo "Test query failed: $@"
    	ERROR=1
    fi
}

# ------------------------------------------------------------------------------
# Set PostgreSQL users and permissions
# ------------------------------------------------------------------------------
sudo cp $TRAVIS_BUILD_DIR/travis/pg_hba.conf `find /etc/postgresql/*/*/pg_hba.conf`
sudo /etc/init.d/postgresql restart

# Disable password (better: create new user)
sudo -u $DBUSER psql -c "ALTER ROLE postgres WITH PASSWORD '';"

# ------------------------------------------------------------------------------
# CREATE DATABASE
# ------------------------------------------------------------------------------
run_psql -U $DBUSER -c "CREATE DATABASE $DBNAME;"

# ------------------------------------------------------------------------------
# CREATE EXTENSION
# ------------------------------------------------------------------------------

if [ "$POSTGIS_VERSION" == "1.5" ]; then
    run_psql -U $DBUSER -d $DBNAME -f $POSTGRESQL_DIRECTORY/contrib/postgis-1.5/postgis.sql
    run_psql -U $DBUSER -d $DBNAME -f $POSTGRESQL_DIRECTORY/contrib/postgis-1.5/spatial_ref_sys.sql

    run_psql -U $DBUSER -d $DBNAME -c "GRANT ALL ON geometry_columns TO PUBLIC;"
    run_psql -U $DBUSER -d $DBNAME -c "GRANT ALL ON geography_columns TO PUBLIC;"
    run_psql -U $DBUSER -d $DBNAME -c "GRANT ALL ON spatial_ref_sys TO PUBLIC;"
else
    run_psql -U $DBUSER -d $DBNAME -c "CREATE EXTENSION postgis;"
fi

run_psql -U $DBUSER -d $DBNAME -c "SELECT postgis_full_version();"

# Return success or failure
# ------------------------------------------------------------------------------
exit $ERROR
