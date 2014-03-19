#!/bin/sh

createdb -U postgres doctrine_postgis_tests

if [[ "$POSTGIS_VERSION" == "2.0" ]]; then
  psql -U postgres -d doctrine_postgis_tests -c "CREATE EXTENSION postgis;"
else
  createlang -U postgres plpgsql doctrine_postgis_tests
  psql -U postgres -d doctrine_postgis_tests -f /usr/share/postgresql/9.1/contrib/postgis-1.5/postgis.sql
  psql -U postgres -d doctrine_postgis_tests -f /usr/share/postgresql/9.1/contrib/postgis-1.5/spatial_ref_sys.sql
fi

sudo service postgresql stop;
sudo service postgresql start $POSTGRESQL_VERSION;
