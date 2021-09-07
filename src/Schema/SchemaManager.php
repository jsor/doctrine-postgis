<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Connection;

final class SchemaManager
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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
        $tableIndexes = $this->connection->fetchAllAssociative(
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

            $stmt = $this->connection->executeQuery($sql);

            /** @var array<array{attname: string, typname: string}> $indexColumns */
            $indexColumns = $stmt->fetchAllAssociative();

            foreach ($indexColumns as $indexRow) {
                if ('geometry' !== $indexRow['typname'] &&
                    'geography' !== $indexRow['typname']) {
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
        $row = $this->connection->fetchAssociative(
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
        $row = $this->connection->fetchAssociative(
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
}
