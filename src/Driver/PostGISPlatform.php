<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\TableDiff;
use InvalidArgumentException;
use Jsor\Doctrine\PostGIS\Schema\SchemaManager;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

use function count;
use function implode;
use function sprintf;

final class PostGISPlatform extends PostgreSQLPlatform
{
    protected function initializeDoctrineTypeMappings(): void
    {
        parent::initializeDoctrineTypeMappings();

        // Map PostgreSQL native geometry/geography types to Doctrine PostGIS types
        // This is essential for schema introspection to work correctly
        $this->doctrineTypeMapping['geometry'] = PostGISType::GEOMETRY;
        $this->doctrineTypeMapping['geography'] = PostGISType::GEOGRAPHY;
    }

    public function createSchemaManager(Connection $connection): PostgreSQLSchemaManager
    {
        /** @var PostgreSQLPlatform $platform */
        $platform = $connection->getDatabasePlatform();

        return new SchemaManager($connection, $platform);
    }

    /**
     * @param string $table
     */
    public function getCreateIndexSQL(Index $index, $table): string
    {
        // Standard PostgreSQL index
        if (!$index->hasFlag('spatial')) {
            return parent::getCreateIndexSQL($index, $table);
        }

        // Handle spatial indexes with GIST
        $name = $index->getQuotedName($this);
        $columns = $index->getColumns();

        if (0 === count($columns)) {
            throw new InvalidArgumentException(sprintf(
                'Incomplete or invalid index definition %s on table %s',
                $name,
                $table,
            ));
        }

        if ($index->isPrimary()) {
            return $this->getCreatePrimaryKeySQL($index, $table);
        }

        $query = 'CREATE ' . $this->getCreateIndexSQLFlags($index) . 'INDEX ' . $name . ' ON ' . $table;
        $query .= ' USING gist(' . implode(', ', $index->getQuotedColumns($this)) . ')';

        // Support partial indexes (WHERE clause)
        $query .= $this->getPartialIndexSQL($index);

        return $query;
    }

    /**
     * @param string|Index $nameOrIndex
     *
     * @psalm-suppress ParamNameMismatch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress InternalMethod
     * @psalm-suppress InvalidArgument
     */
    public function getIndexDeclarationSQL($nameOrIndex, ?Index $index = null): string
    {
        // DBAL 4.x: single Index parameter
        // DBAL 3.x: name + Index parameters
        if ($nameOrIndex instanceof Index) {
            $actualIndex = $nameOrIndex;
        } else {
            $actualIndex = $index;
        }

        // Spatial indexes cannot be declared inline in CREATE TABLE
        // They will be created separately via getCreateIndexSQL
        if ($actualIndex->hasFlag('spatial')) {
            return '';
        }

        if ($nameOrIndex instanceof Index) {
            // DBAL 4.x
            return parent::getIndexDeclarationSQL($nameOrIndex);
        }

        // DBAL 3.x
        return parent::getIndexDeclarationSQL($nameOrIndex, $actualIndex);
    }

    public function getAlterTableSQL(TableDiff $diff): array
    {
        // getCreateIndexSQL will handle spatial indexes with USING gist automatically
        $sql = parent::getAlterTableSQL($diff);

        // Handle SRID updates for spatial columns
        $table = $diff->getOldTable();

        /** @psalm-suppress DeprecatedMethod */
        $modifiedColumns = method_exists($diff, 'getChangedColumns')
            ? $diff->getChangedColumns()
            : @$diff->getModifiedColumns();

        /** @var ColumnDiff $columnDiff */
        foreach ($modifiedColumns as $columnDiff) {
            $oldColumn = $columnDiff->getOldColumn();
            $newColumn = $columnDiff->getNewColumn();
            /** @var int|null $oldSrid */
            $oldSrid = $oldColumn->hasPlatformOption('srid') ? $oldColumn->getPlatformOption('srid') : null;
            /** @var int|null $newSrid */
            $newSrid = $newColumn->hasPlatformOption('srid') ? $newColumn->getPlatformOption('srid') : null;

            if (null === $oldSrid && null === $newSrid) {
                continue;
            }

            if (null !== $newSrid && $oldSrid !== $newSrid) {
                $sql[] = sprintf(
                    "SELECT UpdateGeometrySRID('%s', '%s', %d)",
                    $table->getName(),
                    $newColumn->getName(),
                    $newSrid
                );
            }
        }

        return $sql;
    }
}
