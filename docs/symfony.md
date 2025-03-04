Symfony
==

Before integrating this library into a Symfony project, read the general 
[installation instructions](../README.md#installation) and install the library
via Composer.

* [Known Problems](#known-problems)

### Database Types

Register the DBAL types in the
[doctrine section](https://symfony.com/doc/current/reference/configuration/doctrine.html)
of the `config/packages/doctrine.yaml` config file.

```yaml
doctrine:
    dbal:
        types:
            geography:
                class: 'Jsor\Doctrine\PostGIS\Types\GeographyType'
                commented: false
            geometry:
                class: 'Jsor\Doctrine\PostGIS\Types\GeometryType'
                commented: false
```

### DQL Functions

To use the DQL functions provided by this library, they must be configured in
`config/packages/doctrine.yaml`.

```yaml
doctrine:
    orm:
        dql:
            string_functions:
              ST_Within: Jsor\Doctrine\PostGIS\Functions\ST_Within
              # ...other string functions
            numeric_functions:
              ST_Distance: Jsor\Doctrine\PostGIS\Functions\ST_Distance
              # ...other numeric functions
```

Known Problems
--

### PostGIS Schema Exclusion

Since PostGIS can add a few new schemas, like `topology`, `tiger` and
`tiger_data`, you might want to exclude them from being handled by Doctrine,
especially when you use the [Doctrine Migrations Bundle](https://www.doctrine-project.org/projects/doctrine-migrations-bundle.html).

This can be done by configuring the `schema_filter` option in
`config/packages/doctrine.yaml`.

```yaml
doctrine:
    dbal:
        schema_filter: ~^(?!tiger)(?!topology)~
```

See also [Manual Tables](https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html#manual-tables)
in the Symfony documentation.

### Unknown Database Types

Sometimes, the schema tool stumbles upon database types it can't handle.
A common exception is something like

```
Doctrine\DBAL\Exception: Unknown database type _text requested, Doctrine\DBAL\Platforms\PostgreSQL100Platform may not support it.
```

To solve this, the unknown database types can be mapped to known types with the
`mapping_types` option in `config/packages/doctrine.yaml`.

```yaml
doctrine:
    dbal:
        mapping_types:
            _text: string
```

**Note:** This type is then not suited to be used in entity mappings.
It just prevents "Unknown database type..." exceptions thrown during database
inspections by the schema tool.

If you want to use this type in your entities, you have to configure real
database types, e.g. with the [PostgreSQL for Doctrine](https://github.com/martin-georgiev/postgresql-for-doctrine)
package.
