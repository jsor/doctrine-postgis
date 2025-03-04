PostGIS extension for Doctrine
==============================

[![Build Status](https://github.com/jsor/doctrine-postgis/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/jsor/doctrine-postgis/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/jsor/doctrine-postgis/badge.svg?branch=main&service=github)](https://coveralls.io/github/jsor/doctrine-postgis?branch=main)

This library allows you to use [Doctrine](https://www.doctrine-project.org/)
(ORM or DBAL) with [PostGIS](https://postgis.net/), the spatial database
extension for [PostgreSQL](https://www.postgresql.org/).

* [Supported Versions](#supported-versions)
* [Installation](#installation)
* [Symfony Setup](#symfony-setup)
* [Property Mapping](#property-mapping)
* [Spatial Indexes](#spatial-indexes)
* [Schema Tool](#schema-tool)
* [DQL Functions](#dql-functions)
* [Known Problems](#known-problems)
* [Running the Tests](#running-the-tests)

Supported Versions
--

The following table shows the versions which are officially supported by this
library.

| Dependency    | Supported Versions |
|:--------------|:-------------------|
| PostGIS       | 3.0 and 3.1        |
| PostgreSQL    | < 15               |
| Doctrine ORM  | ^3.0.0             |
| Doctrine DBAL | ^4.1.1             |

Installation
--

Install the latest version with [Composer](https://getcomposer.org).

```bash
composer require jsor/doctrine-postgis
```

Check the [Packagist page](https://packagist.org/packages/jsor/doctrine-postgis)
for all available versions.

Symfony Setup
--

**Manual Bundle Registration**

If Symfony Flex does not automatically register the bundle, you can manually add it to your ``config/bundles.php`` file:

```php
return [
    // Other bundles...
    Jsor\Doctrine\PostGIS\JsorDoctrinePostgisBundle::class => ['all' => true],
];
```

For integrating this library into a Symfony project, configure the schema manager factory in your ``doctrine.yaml``:

```php
# config/packages/doctrine.yaml
doctrine:
    dbal:
        schema_manager_factory: Jsor\Doctrine\PostGIS\Schema\PostGISSchemaManagerFactory
        # rest of your configuration...
```

Property Mapping
--

Once the event subscriber is registered, the column types `geometry` and
`geography` can be used in property mappings (please read the
[PostGIS docs](https://postgis.net/docs/using_postgis_dbmanagement.html#PostGIS_Geography)
to understand the difference between these two types).

```php
use Doctrine\ORM\Mapping as ORM;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

#[ORM\Entity]
class MyEntity
{
    #[ORM\Column(type: PostGISType::GEOMETRY)]
    private string $geometry;

    #[ORM\Column(type: PostGISType::GEOGRAPHY)]
    private string $geography;
}
```

There are 2 options to configure the geometry.

* `geometry_type`
  This defines the type of the geometry, like `POINT`, `LINESTRING` etc.
  If you omit this option, the generic type `GEOMETRY` is used.
* `srid`
  This defines the Spatial Reference System Identifier (SRID) of the geometry.

### Example

```php
use Doctrine\ORM\Mapping as ORM;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

#[ORM\Entity]
class MyEntity
{
    #[ORM\Column(
        type: PostGISType::GEOMETRY, 
        options: ['geometry_type' => 'POINT'],
    )]
    public string $point;

    #[ORM\Column(
        type: PostGISType::GEOMETRY, 
        options: ['geometry_type' => 'POINTZM'],
   )]
    public string $point4D;

    #[ORM\Column(
        type: PostGISType::GEOMETRY, 
        options: ['geometry_type' => 'POINT', 'srid' => 3785],
    )]
    public string $pointWithSRID;

    public function __construct(
        string $point,
        string $point4D,
        string $pointWithSRID,
    ) {
        $this->point = $point;
        $this->point4D = $point4D;
        $this->pointWithSRID = $pointWithSRID;
    }
}
```

Values provided for the properties must be in the [WKT](https://en.wikipedia.org/wiki/Well-known_text)
format. Please note, that the values returned from database may differ from the
values you have set. The library uses [ST_AsEWKT](https://postgis.net/docs/ST_AsEWKT.html)
to retain as much information as possible (like SRID's). Read more in the
[PostGIS docs](https://postgis.net/docs/using_postgis_dbmanagement.html#RefObject).

### Example

```php
$entity = new MyEntity(
    point: 'POINT(-122.0845187 37.4220761)',
    point4D: 'POINT(1 2 3 4)',
    pointWithSRID: 'SRID=3785;POINT(-122.0845187 37.4220761)',
);
```

Spatial Indexes
--

[Spatial indexes](https://postgis.net/docs/using_postgis_dbmanagement.html#gist_indexes)
can be defined for geometry fields by setting the `spatial` flag.

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(
    fields: ['pointWithSRID'],
    flags: ['spatial'],
)]
class MyEntity
{
}
```

Schema Tool
--

Full support for
the [ORM Schema Tool](https://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/tools.html)
and
the [DBAL Schema Manager](https://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/schema-manager.html)
is provided.

DQL Functions
--

Most [PostGIS functions](https://postgis.net/docs/reference.html) are also
available for
the [Doctrine Query Language](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html)
(DQL) under the `Jsor\Doctrine\PostGIS\Functions` namespace.

For a full list of all supported functions, see the
[Function Index](docs/function-index.md).

> Read the dedicated [Symfony documentation](docs/symfony.md#dql-functions) on
> how to configure the functions with Symfony.

The functions must be registered with the `Doctrine\ORM\Configuration` instance.

```php
$configuration = new Doctrine\ORM\Configuration();

$configuration->addCustomStringFunction(
    'ST_Within',
    Jsor\Doctrine\PostGIS\Functions\ST_Within::class
);

$configuration->addCustomNumericFunction(
    'ST_Distance',
    Jsor\Doctrine\PostGIS\Functions\ST_Distance::class
);

$dbParams = [/***/];
$entityManager = Doctrine\ORM\EntityManager::create($dbParams, $configuration);
```

There's a convenience Configurator class which can be used to register all
functions at once.

```php
$configuration = new Doctrine\ORM\Configuration();

Jsor\Doctrine\PostGIS\Functions\Configurator::configure($configuration);

$dbParams = [/***/];
$entityManager = Doctrine\ORM\EntityManager::create($dbParams, $configuration);
```

Known Problems
--

> Read the dedicated [Symfony documentation](docs/symfony.md#known-problems) on
> how to handle those problems with Symfony.

### PostGIS Schema Exclusion

Since PostGIS can add a few new schemas, like `topology`, `tiger` and
`tiger_data`, you might want to exclude them from being handled by Doctrine.

This can be done by configuring a schema assets filter.

```php
$configuration = new Doctrine\ORM\Configuration();

$configuration->setSchemaAssetsFilter(static function ($assetName): bool {
     if ($assetName instanceof AbstractAsset) {
         $assetName = $assetName->getName();
     }

     return (bool) preg_match('/^(?!tiger)(?!topology)/', $assetName);
});

$dbParams = [/***/];
$entityManager = Doctrine\ORM\EntityManager::create($dbParams, $configuration);
```

### Unknown Database Types

Sometimes, the schema tool stumbles upon database types it can't handle.
A common exception is something like

```
Doctrine\DBAL\Exception: Unknown database type _text requested, Doctrine\DBAL\Platforms\PostgreSQL100Platform may not support it.
```

To solve this, the unknown database types can be mapped to known types.

```php
$configuration = new Doctrine\ORM\Configuration();

$dbParams = [/***/];
$entityManager = Doctrine\ORM\EntityManager::create($dbParams, $configuration);

$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('_text', 'string');
```

**Note:** This type is then not suited to be used in entity mappings.
It just prevents "Unknown database type..." exceptions thrown during database
inspections by the schema tool.

If you want to use this type in your entities, you have to configure real
database types, e.g. with the [PostgreSQL for Doctrine](https://github.com/martin-georgiev/postgresql-for-doctrine)
package.

Running the Tests
--

A simple Docker setup is included to run the test suite against the different
PostgreSQL / PostGIS combinations.

All commands here should be run from the project root.

First, build the PHP container. This must be done only once.

```bash
./docker/build-php.sh
```

Install dependencies via Composer.

```bash
./docker/run-php.sh composer install
```

Next, start the database containers.

```bash
docker compose -f ./docker/docker-compose.yml up -d
```

There are a number of shortcut scripts available to execute commands inside the
PHP container connected to specific database containers.

The script names follow the pattern
`run-<POSTGRESQL_VERSION>-<POSTGIS_VERSION>.sh`.

To run the test suite against PostgreSQL 13 with PostGIS 3.1, use the script
`./docker/run-13-31.sh`.

```bash
./docker/run-13-31.sh vendor/bin/phpunit --exclude-group=postgis-3.0
```

Note, that we exclude tests targeted at PostGIS 3.0 here. When running tests
against PostGIS 3.0, exclude the tests for 3.1.

```bash
./docker/run-13-30.sh vendor/bin/phpunit --exclude-group=postgis-3.1
```

License
--

Copyright (c) 2014-2024 Jan Sorgalla.
Released under the [MIT License](LICENSE).
