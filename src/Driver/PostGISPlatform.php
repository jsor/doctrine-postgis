<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Index;
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
        $spatialIndexes = [];
        foreach ($diff->getAlteredTables() as $tableDiff) {
            $table = $tableDiff->getOldTable();
            if (!$table) {
                continue;
            }

            /** @var Index[] $indices */
            $indices = [];
            foreach (SpatialIndexes::ensureTableDiffFlag($tableDiff) as $index) {
                $indices[] = $index;
            }
            $spatialIndexes[$table->getName()] = ['table' => $table, 'indexes' => $indices];

            SpatialIndexes::filterTableDiff($tableDiff);
        }

        $sql = parent::getAlterSchemaSQL($diff);

        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($this);
        foreach ($spatialIndexes as $spatialIndex) {
            /** @var Table $table */
            $table = $spatialIndex['table'];
            /** @var Index $index */
            foreach ($spatialIndex['indexes'] as $index) {
                $sql[] = $spatialIndexSqlGenerator->getSql($index, $table);
            }
        }

        return $sql;
    }

    public function getCreateTableSQL(Table $table, $createFlags = self::CREATE_INDEXES): array
    {
        $spatialIndexes = SpatialIndexes::ensureTableFlag($table);

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
            $spatialIndexes = SpatialIndexes::ensureTableFlag($table);

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
        $spatialIndexes = [];
        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($this);

        if ($table) {
            $spatialIndexes = SpatialIndexes::ensureTableDiffFlag($diff);
        }

        SpatialIndexes::filterTableDiff($diff);

        $sql = parent::getAlterTableSQL($diff);

        if (!$table) {
            return $sql;
        }

        foreach ($spatialIndexes as $spatialIndex) {
            $sql[] = $spatialIndexSqlGenerator->getSql($spatialIndex, $table);
        }

        /** @var ColumnDiff $columnDiff */
        foreach ($diff->getModifiedColumns() as $columnDiff) {
            if ($columnDiff->hasChanged('srid')) {
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
