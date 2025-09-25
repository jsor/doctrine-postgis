<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
use RuntimeException;

final class SchemaManager extends PostgreSQLSchemaManager
{
    public function alterTable(TableDiff $tableDiff): void
    {
        $oldTable = $tableDiff->getOldTable();

        /** @psalm-suppress DeprecatedMethod */
        $modifiedColumns = method_exists($tableDiff, 'getChangedColumns')
            ? $tableDiff->getChangedColumns()
            : @$tableDiff->getModifiedColumns();

        foreach ($modifiedColumns as $columnDiff) {
            if (!$columnDiff->getNewColumn()->getType() instanceof PostGISType) {
                continue;
            }

            $newColumn = $columnDiff->getNewColumn();
            $oldColumn = $columnDiff->getOldColumn();

            if ($columnDiff->hasTypeChanged()) {
                $oldType = Type::lookupName($oldColumn->getType());
                $newType = Type::lookupName($newColumn->getType());

                throw new RuntimeException('The type of a spatial column cannot be changed (Requested changing type from "' . $oldType . '" to "' . $newType . '" for column "' . $newColumn->getName() . '" in table "' . $oldTable->getName() . '")');
            }

            $oldGT = (string) ($oldColumn->hasPlatformOption('geometry_type') ? $oldColumn->getPlatformOption('geometry_type') : 'N/A');
            $newGT = (string) ($newColumn->hasPlatformOption('geometry_type') ? $newColumn->getPlatformOption('geometry_type') : 'N/A');
            if (strtolower($oldGT) !== strtolower($newGT)) {
                throw new RuntimeException('The geometry_type of a spatial column cannot be changed (Requested changing type from "' . strtoupper($oldGT) . '" to "' . strtoupper($newGT) . '" for column "' . $newColumn->getName() . '" in table "' . $oldTable->getName() . '")');
            }
        }

        parent::alterTable($tableDiff);
    }

    public function introspectTable(string $name): Table
    {
        $table = parent::introspectTable($name);

        SpatialIndexes::ensureSpatialIndexFlags($table);

        return $table;
    }

    /**
     * @param string $table
     *
     * @return array<string, Index>
     */
    public function listTableIndexes($table): array
    {
        $indexes = parent::listTableIndexes($table);
        $columns = $this->listTableColumns($table);

        foreach ($indexes as $index) {
            foreach ($index->getColumns() as $columnName) {
                $column = $columns[$columnName];
                if ($column->getType() instanceof PostGISType && !$index->hasFlag('spatial')) {
                    $index->addFlag('spatial');
                }
            }
        }

        return $indexes;
    }

    public function listSpatialIndexes(string $table): array
    {
        if (str_contains($table, '.')) {
            [, $table] = explode('.', $table);
        }

        $sql = "SELECT distinct i.relname, d.indkey, pg_get_indexdef(d.indexrelid) AS inddef, t.oid
                FROM pg_class t
                INNER JOIN pg_index d ON t.oid = d.indrelid
                INNER JOIN pg_class i ON d.indexrelid = i.oid
                WHERE i.relkind = 'i'
                AND d.indisprimary = 'f'
                AND t.relname = ?
                AND i.relnamespace IN (SELECT oid FROM pg_namespace WHERE nspname = ANY (current_schemas(false)) )
                ORDER BY i.relname";

        /** @var array<array{relname: string, indkey: string, inddef: string, oid: string}> $tableIndexes */
        $tableIndexes = $this->getConnection()->fetchAllAssociative(
            $sql,
            [
                $this->trimQuotes($table),
            ]
        );

        $indexes = [];
        foreach ($tableIndexes as $row) {
            if (!preg_match('/using\s+gist/i', $row['inddef'])) {
                continue;
            }

            $sql = "SELECT a.attname, t.typname
                    FROM pg_attribute a, pg_type t
                    WHERE a.attrelid = {$row['oid']}
                    AND a.attnum IN (" . implode(',', explode(' ', $row['indkey'])) . ')
                    AND a.atttypid = t.oid';

            $stmt = $this->getConnection()->executeQuery($sql);

            /** @var array<array{attname: string, typname: string}> $indexColumns */
            $indexColumns = $stmt->fetchAllAssociative();

            foreach ($indexColumns as $indexRow) {
                if ('geometry' !== $indexRow['typname']
                    && 'geography' !== $indexRow['typname']) {
                    continue;
                }

                if (!isset($indexes[$row['relname']])) {
                    $indexes[$row['relname']] = [];
                }

                $indexes[$row['relname']][] = trim($indexRow['attname']);
            }
        }

        return $indexes;
    }

    public function getGeometrySpatialColumnInfo(string $table, string $column): ?array
    {
        if (str_contains($table, '.')) {
            [, $table] = explode('.', $table);
        }

        $sql = 'SELECT coord_dimension, srid, type
                FROM geometry_columns
                WHERE f_table_name = ?
                AND f_geometry_column = ?';

        /** @var array{coord_dimension: string, srid: string|int|null, type: string}|null $row */
        $row = $this->getConnection()->fetchAssociative(
            $sql,
            [
                $this->trimQuotes($table),
                $this->trimQuotes($column),
            ]
        );

        if (!$row) {
            return null;
        }

        return $this->buildSpatialColumnInfo($row);
    }

    public function getGeographySpatialColumnInfo(string $table, string $column): ?array
    {
        if (str_contains($table, '.')) {
            [, $table] = explode('.', $table);
        }

        $sql = 'SELECT coord_dimension, srid, type
                FROM geography_columns
                WHERE f_table_name = ?
                AND f_geography_column = ?';

        /** @var array{coord_dimension: string, srid: string|int|null, type: string}|null $row */
        $row = $this->getConnection()->fetchAssociative(
            $sql,
            [
                $this->trimQuotes($table),
                $this->trimQuotes($column),
            ]
        );

        if (!$row) {
            return null;
        }

        return $this->buildSpatialColumnInfo($row);
    }

    /**
     * @param string                           $table
     * @param string                           $database
     * @param array<int, array<string, mixed>> $tableColumns
     *
     * @return array<string, Column>
     */
    protected function _getPortableTableColumnList($table, $database, $tableColumns): array
    {
        $columns = parent::_getPortableTableColumnList($table, $database, $tableColumns);

        foreach ($columns as $column) {
            $this->resolveSpatialColumnInfo($column, $table);
        }

        return $columns;
    }

    protected function _getPortableTableColumnDefinition($tableColumn): Column
    {
        $column = parent::_getPortableTableColumnDefinition($tableColumn);

        if (isset($tableColumn['table_name'])) {
            $this->resolveSpatialColumnInfo($column, (string) $tableColumn['table_name']);
        }

        return $column;
    }

    protected function resolveSpatialColumnInfo(Column $column, string $tableName): void
    {
        if (!$column->getType() instanceof PostGISType) {
            return;
        }

        $info = match ($column->getType()::class) {
            GeometryType::class => $this->getGeometrySpatialColumnInfo($tableName, $column->getName()),
            GeographyType::class => $this->getGeographySpatialColumnInfo($tableName, $column->getName()),
            default => null,
        };

        if (null === $info) {
            return;
        }

        $default = null;

        if ('NULL::geometry' !== $column->getDefault() && 'NULL::geography' !== $column->getDefault()) {
            $default = $column->getDefault();
        }

        $column
            ->setType(PostGISType::getType(Type::lookupName($column->getType())))
            ->setDefault($default)
            ->setPlatformOption('geometry_type', $info['type'])
            ->setPlatformOption('srid', $info['srid'])
        ;
    }

    /**
     * @param array{coord_dimension: string, srid: string|int|null, type: string} $row
     */
    private function buildSpatialColumnInfo(array $row): array
    {
        $type = strtoupper($row['type']);

        if (!str_ends_with($type, 'M')) {
            if (4 === (int) $row['coord_dimension']) {
                $type .= 'ZM';
            }

            if (3 === (int) $row['coord_dimension']) {
                $type .= 'Z';
            }
        }

        return [
            'type' => $type,
            'srid' => max((int) $row['srid'], 0),
        ];
    }

    /**
     * Copied from Doctrine\DBAL\Schema\AbstractAsset::trimQuotes,
     * check on updates!
     */
    private function trimQuotes(string $identifier): string
    {
        return str_replace(['`', '"', '[', ']'], '', $identifier);
    }

    private function getConnection(): Connection
    {
        // DBAL v3
        if (property_exists($this, '_conn')) {
            return $this->_conn;
        }

        return $this->connection;
    }
}
