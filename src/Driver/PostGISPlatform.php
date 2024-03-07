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
        $sql = parent::getAlterSchemaSQL(SpatialIndexes::filterSchemaDiff($diff));

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

        $sql = parent::getAlterTableSQL(SpatialIndexes::filterTableDiff($diff));

        foreach (SpatialIndexes::extractSpatialIndicies($diff->getAddedIndexes()) as $spatialIndex) {
            $sql[] = $spatialIndexSqlGenerator->getSql($spatialIndex, $table);
        }

        foreach (SpatialIndexes::extractSpatialIndicies($diff->getModifiedIndexes()) as $index) {
            $sql[] = $this->getDropIndexSQL($index->getName(), $table->getName());
            $sql[] = $spatialIndexSqlGenerator->getSql($index, $table);
        }

        /** @var ColumnDiff $columnDiff */
        foreach ($diff->getModifiedColumns() as $columnDiff) {
            if ($columnDiff->getOldColumn()->getPlatformOption('srid') !== $columnDiff->getNewColumn()->getPlatformOption('srid')) {
                $sql[] = sprintf(
                    "SELECT UpdateGeometrySRID('%s', '%s', %d)",
                    $table->getName(),
                    $columnDiff->getNewColumn()->getName(),
                    (int) $columnDiff->getNewColumn()->getPlatformOption('srid')
                );
            }
        }

        return $sql;
    }
}
