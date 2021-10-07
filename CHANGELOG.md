Changelog
=========

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](https://semver.org).

2.0.0 - 2021-10-07
--

New major release which introduces a new dependency version support matrix.

| Dependency    | Supported Versions  |
|:--------------|:--------------------|
| PHP           | ^8.0                |
| PostGIS       | 3.0 and 3.1         |
| PostgreSQL    | 11, 12 and 13       |
| Doctrine ORM  | ^2.9                |
| Doctrine DBAL | ^2.13 and ^3.1      |

### Other Changes

* The following DQL functions have been removed, mostly because they are also no
  longer available in PostGIS:

  * `ST_MakeBox3D` (use `ST_3DMakeBox` instead)
  * `ST_AsKML`
  * `ST_Distance_Sphere` (use `ST_DistanceSphere` instead)
  * `ST_Distance_Spheroid` (use `ST_DistanceSpheroid` instead)
  * `ST_Length3D` (use `ST_3DLength` instead)
  * `ST_Length_Spheroid` (use `ST_LengthSpheroid` instead)
  * `ST_Length2D_Spheroid`
  * `ST_Shift_Longitude` (use `ST_ShiftLongitude` instead)
* The dummy `RasterType` has been removed. Register a custom type mapping
  instead, e.g. `$platform->registerDoctrineTypeMapping('raster', 'string')`.
* Add parameter and return type declarations.
* Switch from Travis CI to GitHub Actions for running CI pipeline.
* Add Docker setup to allow running tests against the different supported
  PostgreSQL / PostGIS versions.

1.8.0 - 2021-09-03
------------------

* This release now allows installation on PHP 8.

  **Note**: While it is possible to install this library on
  PHP 8, it is **not** officially support right now. Use with care and at your
  own risk.

1.7.0 - 2018-08-01
------------------

  * This release drops support for the EOL versions of PHP 5.3, 5.4 and 5.5.

1.6.0 - 2018-07-13
------------------

  * Fix: Remove setting unsupported `primary` option when creating the
    `Doctrine\DBAL\Schema\Column` object. This prevents triggering a deprecation
    error for DBAL >= 2.7. Thanks @simonwelsh (#38).
  * Feature: Run test against PHP 7.2. Thanks @carusogabriel (#35).

1.5.0 - 2017-11-13
------------------

  * Feature: New functions `ST_DistanceSphere`, `ST_DistanceSpheroid` and
    `ST_LengthSpheroid`. Those functions should be used in PostGIS 2.2 and
    onwards instead of the deprecated functions `ST_Distance_Sphere`,
    `ST_Distance_Spheroid` and  `ST_Length_Spheroid`. Thanks to @JunkMyFunk for
    the report (#32).
  * Feature: Tests now use namespaced classes from PHPUnit.
    Thanks @carusogabriel (#33).

1.4.1 - 2017-07-12
------------------

  * Fix: Ensure SRID values from metadata are casted to int to prevent
    unecessary schema diffs when using XML mappings. Thanks to @HenningCash for
    the report (#30).

1.4.0 - 2017-01-11
------------------

  * Feature: New function `ST_AddPoint`. Thanks @ltsstar (#26).
  * Feature: New function `Geography`.

1.3.0 - 2016-12-16
------------------

  * Feature: New function `Geometry`. Thanks @dragosprotung (#22).

1.2.0 - 2016-12-15
------------------

  * Feature: New function `ST_SnapToGrid`. Thanks @dragosprotung (#21).

1.1.2 - 2016-11-28
------------------

  * Fix: Fix handling of table and column names using reserved words. Thanks to
    @maximilian-walter for reporting (#19).

1.1.1 - 2016-10-11
------------------

  * Fix: The event subscribers now allow multiple calls to `postConnect` for the
    same connection instance. This is done by MasterSlaveConnection for example
    when switching master/slave connections. Thanks to @gcavana for reporting,
    patch courtesy of @NoiseByNorthwest in #17.

1.1.0 - 2016-04-08
------------------

  * Feature: New [raster](https://postgis.net/docs/raster.html) DBAL type.
    Note: This type is not suited to be used in entity mappings.
    It just prevents "Unknown database type..." exceptions thrown during
    database inspections by the schema tool.

1.0.0 - 2015-08-11
------------------

  * First stable release.
