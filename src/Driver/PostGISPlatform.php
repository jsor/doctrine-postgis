<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Jsor\Doctrine\PostGIS\Schema\SchemaManager;
use Jsor\Doctrine\PostGIS\Schema\SpatialIndexes;
use Jsor\Doctrine\PostGIS\Schema\SpatialIndexSqlGenerator;

use function sprintf;

final class PostGISPlatform extends PostgreSQLPlatform
{
    public function createSchemaManager(Connection $connection): PostgreSQLSchemaManager
    {
        /** @var PostgreSQLPlatform $platform */
        $platform = $connection->getDatabasePlatform();

        return new SchemaManager($connection, $platform);
    }

    public function getAlterSchemaSQL(SchemaDiff $diff): array
    {
        /** @var list<string> $sql */
        $sql = parent::getAlterSchemaSQL($diff);
        $sql = $this->filterSpatialIndexFromSQL($sql, $this->collectSpatialIndexNamesFromSchemaDiff($diff));

        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($this);

        foreach ($diff->getAlteredTables() as $tableDiff) {
            $table = $tableDiff->getOldTable();

            SpatialIndexes::ensureSpatialIndexFlags($tableDiff);

            foreach (SpatialIndexes::extractSpatialIndicies($tableDiff->getAddedIndexes()) as $index) {
                $sql[] = $spatialIndexSqlGenerator->getSql($index, $table);
            }

            foreach (SpatialIndexes::extractSpatialIndicies($tableDiff->getModifiedIndexes()) as $index) {
                $sql[] = $this->getDropIndexSQL($index->getName(), $table->getName());
                $sql[] = $spatialIndexSqlGenerator->getSql($index, $table);
            }
        }

        return $sql;
    }

    public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES): array
    {
        SpatialIndexes::ensureSpatialIndexFlags($table);

        $spatialIndexes = SpatialIndexes::extractSpatialIndicies($table->getIndexes());
        foreach ($spatialIndexes as $index) {
            $table->dropIndex($index->getName());
        }

        $sql = parent::getCreateTableSQL($table, $createFlags);

        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($this);
        foreach ($spatialIndexes as $index) {
            $sql[] = $spatialIndexSqlGenerator->getSql($index, $table);
        }

        return $sql;
    }

    public function getCreateTablesSQL(array $tables): array
    {
        $sql = [];
        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($this);

        /** @var Table $table */
        foreach ($tables as $table) {
            SpatialIndexes::ensureSpatialIndexFlags($table);

            $spatialIndexes = SpatialIndexes::extractSpatialIndicies($table->getIndexes());
            foreach ($spatialIndexes as $index) {
                $table->dropIndex($index->getName());
            }
            $sql = [...$sql, ...$this->getCreateTableWithoutForeignKeysSQL($table)];

            foreach ($spatialIndexes as $index) {
                $table->addIndex($index->getColumns(), $index->getName(), $index->getFlags(), $index->getOptions());
            }

            foreach ($spatialIndexes as $spatialIndex) {
                $sql[] = $spatialIndexSqlGenerator->getSql($spatialIndex, $table);
            }
        }

        foreach ($tables as $table) {
            foreach ($table->getForeignKeys() as $foreignKey) {
                $sql[] = $this->getCreateForeignKeySQL(
                    $foreignKey,
                    $table->getQuotedName($this),
                );
            }
        }

        return $sql;
    }

    public function getAlterTableSQL(TableDiff $diff): array
    {
        $table = $diff->getOldTable();
        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($this);

        SpatialIndexes::ensureSpatialIndexFlags($diff);

        /** @var list<string> $sql */
        $sql = parent::getAlterTableSQL($diff);
        $sql = $this->filterSpatialIndexFromSQL($sql, $this->collectSpatialIndexNamesFromTableDiff($diff));

        foreach (SpatialIndexes::extractSpatialIndicies($diff->getAddedIndexes()) as $spatialIndex) {
            $sql[] = $spatialIndexSqlGenerator->getSql($spatialIndex, $table);
        }

        foreach (SpatialIndexes::extractSpatialIndicies($diff->getModifiedIndexes()) as $index) {
            $sql[] = $this->getDropIndexSQL($index->getName(), $table->getName());
            $sql[] = $spatialIndexSqlGenerator->getSql($index, $table);
        }

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

    /**
     * @param list<string> $sql               Generated standard SQL
     * @param list<string> $spatialIndexNames Names of spatial indexes to filter out
     *
     * @return list<string> SQL without spatial index statements
     */
    private function filterSpatialIndexFromSQL(array $sql, array $spatialIndexNames): array
    {
        if (empty($spatialIndexNames)) {
            return $sql;
        }

        $filtered = array_filter($sql, function (string $sqlStatement) use ($spatialIndexNames) {
            foreach ($spatialIndexNames as $indexName) {
                if (false !== stripos($sqlStatement, $indexName)) {
                    return false;
                }
            }

            return true;
        });

        return array_values($filtered);
    }

    /**
     * @param SchemaDiff $diff Schema diff
     *
     * @return list<string> Names of spatial indexes
     */
    private function collectSpatialIndexNamesFromSchemaDiff(SchemaDiff $diff): array
    {
        $spatialIndexNames = [];

        foreach ($diff->getAlteredTables() as $tableDiff) {
            SpatialIndexes::ensureSpatialIndexFlags($tableDiff);
            $spatialIndexNames = array_merge(
                $spatialIndexNames,
                $this->collectSpatialIndexNamesFromTableDiff($tableDiff)
            );
        }

        return $spatialIndexNames;
    }

    /**
     * @param TableDiff $diff Table diff
     *
     * @return list<string> Name of spatial indexes
     */
    private function collectSpatialIndexNamesFromTableDiff(TableDiff $diff): array
    {
        $spatialIndexNames = [];

        foreach (SpatialIndexes::extractSpatialIndicies($diff->getAddedIndexes()) as $index) {
            $spatialIndexNames[] = $index->getName();
        }

        foreach (SpatialIndexes::extractSpatialIndicies($diff->getModifiedIndexes()) as $index) {
            $spatialIndexNames[] = $index->getName();
        }

        return $spatialIndexNames;
    }
}
