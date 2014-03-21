PostGIS extension for Doctrine
==============================

[![Build Status](https://secure.travis-ci.org/jsor/doctrine-postgis.svg?branch=master)](http://travis-ci.org/jsor/doctrine-postgis)

This library allows you to use Doctrine with PostGIS, the spatial database
extension for PostgreSQL.

Both PostGIS 1.5 and 2.x are supported as well as GiST-based spatial indexes.

Installation
------------

Install [through composer](http://getcomposer.org). Check the
[packagist page](https://packagist.org/packages/jsor/doctrine-postgis)
for all available versions.

```json
{
    "require": {
        "jsor/doctrine-postgis": "~0.1.0@dev"
    }
}
```

Setup
-----

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

Usage
-----

### Property Mapping

Once the event subscriber is registered, you can use the columns types
`geometry` and `geography` in your property mappings (please read the
[PostGIS docs](http://postgis.net/docs/manual-2.1/using_postgis_dbmanagement.html#PostGIS_Geography)
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

* `spatial_type`
   This defines the type of the geometry, like POINT, LINESTRING etc.
* `spatial_srid`
  This defines the Spatial Reference System Identifier (SRID) of the geometry.

#### Example

```php
/** @Entity */
class MyEntity
{
    /**
     * @Column(type="geometry", options={"spatial_type"="POINT"})
     */
    private $point;

    /**
     * @Column(type="geometry", options={"spatial_type"="POINTZM"})
     */
    private $point4D;

    /**
     * @Column(type="geometry", options={"spatial_type"="POINT", "spatial_srid"=3785})
     */
    private $pointWithSRID;
}
```

Values provided for the properties must be in the [WKT](http://en.wikipedia.org/wiki/Well-known_text)
format. Please note, that the values returned from database may differ from the
values you have set. The library uses [ST_AsEWKT](http://postgis.net/docs/manual-2.1/ST_AsEWKT.html)
to retain as much information as possible (like SRID's). Read more in the
[PostGIS docs](http://postgis.net/docs/manual-2.1/using_postgis_dbmanagement.html#RefObject).

#### Example

```php
$entity = new MyEntity();

$entity->setPoint('POINT(37.4220761 -122.0845187)');
$entity->setPoint4D('POINT(1 2 3 4)');
$entity->setPointWithSRID('SRID=3785;POINT(37.4220761 -122.0845187)');
```

### Spatial Indexes

You can define [spatial indexes](http://postgis.net/docs/manual-2.1/using_postgis_dbmanagement.html#idp33368240)
for your geometry columns.

In theory, you simply have to define a `SPATIAL` flag in your index definition.

```php
/**
 * @Entity
 * @Table(
 *     indexes={
 *         @Index(name="idx_point", columns={"point"}, flags={"SPATIAL"}))
 *     }
 * )
 */
class MyEntity
{
}
```

In practice, the ORM doesn't support flags yet (but there is a
[pull request](https://github.com/doctrine/doctrine2/pull/973) open to add
support for it).

In the meantime you can use the following workaround to define spatial indexes.

```php
/**
 * @Entity
 * @Table(
 *     options={"spatial_indexes"={"idx_point"}},
 *     indexes={
 *         @Index(name="idx_point", columns={"point"}))
 *     }
 * )
 */
class MyEntity
{
}
```

License
-------

[MIT License](https://github.com/jsor/doctrine-postgis/blob/master/LICENSE).
