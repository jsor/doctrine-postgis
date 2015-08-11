PostGIS extension for Doctrine
==============================

[![Build Status](https://secure.travis-ci.org/jsor/doctrine-postgis.svg?branch=master)](http://travis-ci.org/jsor/doctrine-postgis)
[![Coverage Status](https://coveralls.io/repos/jsor/doctrine-postgis/badge.svg?branch=master&service=github)](https://coveralls.io/github/jsor/doctrine-postgis?branch=master)

This library allows you to use Doctrine with PostGIS, the spatial database
extension for PostgreSQL. Both PostGIS **1.5** and **2.x** are supported.

* [Installation](#installation)
* [Mapping](#mapping)
    * [Setup](#setup)
    * [Property Mapping](#property-mapping)
    * [Spatial Indexes](#spatial-indexes)
    * [Schema Tool](#schema-tool)
* [DQL Functions](#dql-functions)

Installation
------------

Install the latest version with [Composer](http://getcomposer.org).

```bash
composer require jsor/doctrine-postgis
```

Check the [Packagist page](https://packagist.org/packages/jsor/doctrine-postgis)
for all available versions.

Mapping
-------

### Setup

All you have to do is, to register an event subscriber.

```php
use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventSubscriber;

$entityManager->getEventManager()->addEventSubscriber(new ORMSchemaEventSubscriber());
```

You can also use this libray with the DBAL only.

```php
use Jsor\Doctrine\PostGIS\Event\DBALSchemaEventSubscriber;

$connection->getEventManager()->addEventSubscriber(new DBALSchemaEventSubscriber());
```

If you use Symfony, see the [documentation](http://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html)
on how to register event subscribers.

### Property Mapping

Once the event subscriber is registered, you can use the column types
`geometry` and `geography` in your property mappings (please read the
[PostGIS docs](http://postgis.net/docs/using_postgis_dbmanagement.html#PostGIS_Geography)
to understand the difference between these two types).

```php
/** @Entity */
class MyEntity
{
    /**
     * @Column(type="geometry")
     */
    private $geometry;

    /**
     * @Column(type="geography")
     */
    private $geography;
}
```

There are 2 options you can set to define the geometry.

* `geometry_type`
   This defines the type of the geometry, like `POINT`, `LINESTRING` etc.
   If you omit this option, the generic type `GEOMETRY` is used.
* `srid`
   This defines the Spatial Reference System Identifier (SRID) of the geometry.

#### Example

```php
/** @Entity */
class MyEntity
{
    /**
     * @Column(type="geometry", options={"geometry_type"="POINT"})
     */
    private $point;

    /**
     * @Column(type="geometry", options={"geometry_type"="POINTZM"})
     */
    private $point4D;

    /**
     * @Column(type="geometry", options={"geometry_type"="POINT", "srid"=3785})
     */
    private $pointWithSRID;
}
```

Values provided for the properties must be in the [WKT](http://en.wikipedia.org/wiki/Well-known_text)
format. Please note, that the values returned from database may differ from the
values you have set. The library uses [ST_AsEWKT](http://postgis.net/docs/ST_AsEWKT.html)
to retain as much information as possible (like SRID's). Read more in the
[PostGIS docs](http://postgis.net/docs/using_postgis_dbmanagement.html#RefObject).

#### Example

```php
$entity = new MyEntity();

$entity->setPoint('POINT(37.4220761 -122.0845187)');
$entity->setPoint4D('POINT(1 2 3 4)');
$entity->setPointWithSRID('SRID=3785;POINT(37.4220761 -122.0845187)');
```

### Spatial Indexes

You can define [spatial indexes](http://postgis.net/docs/using_postgis_dbmanagement.html#gist_indexes)
for your geometry columns.

Simply set the `SPATIAL` flag for indexes.

```php
/**
 * @Entity
 * @Table(
 *     indexes={
 *         @Index(name="idx_point", columns={"point"}, flags={"SPATIAL"})),
 *         @Index(name="idx_polygon", columns={"polygon"}, flags={"SPATIAL"}))
 *     }
 * )
 */
class MyEntity
{
}
```

This uses [index flags](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/changelog/migration_2_5.html#mapping-allow-configuring-index-flags)
introduced in Doctrine ORM 2.5.

If you need to support Doctrine versions < 2.5, you have to define which indexes
should be spatial indexes through the table options.

```php
/**
 * @Entity
 * @Table(
 *     options={"spatial_indexes"={"idx_point", "idx_polygon"}},
 *     indexes={
 *         @Index(name="idx_point", columns={"point"}),
 *         @Index(name="idx_polygon", columns={"polygon"})
 *     }
 * )
 */
class MyEntity
{
}
```

### Schema Tool

Full support for the [ORM Schema Tool](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/tools.html)
and the [DBAL Schema Manager](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/schema-manager.html)
is provided.

DQL Functions
-------------

Most [PostGIS functions](http://postgis.net/docs/reference.html) are also
available for the DQL under the `Jsor\Doctrine\PostGIS\Functions` namespace.

You can register the functions with a `Doctrine\ORM\Configuration` instance.
 
```php
$configuration = new Doctrine\ORM\Configuration();

$configuration->addCustomStringFunction('ST_Touches', 'Jsor\Doctrine\PostGIS\Functions\ST_Touches');
```

There's a convenience Configurator class which can be used to register all
at once.

```php
$configuration = new Doctrine\ORM\Configuration();

Jsor\Doctrine\PostGIS\Functions\Configurator::configure($configuration);
```
 
For a full list of all supported functions, see the [Function Index](docs/function-index.md).

License
-------

Copyright (c) 2014-2015 Jan Sorgalla.
Released under the [MIT License](https://github.com/jsor/doctrine-postgis/blob/master/LICENSE).
