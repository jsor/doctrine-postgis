Changelog
=========

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org).

1.1.0 - 2016-04-08
------------------

  * Feature: New [raster](http://postgis.net/docs/raster.html) DBAL type.
    Note: This type is not suited to be used in entity mappings.
    It just prevents "Unknown database type..." exceptions thrown during
    database inspections by the schema tool.

1.0.0 - 2015-08-11
------------------

  * First stable release.
