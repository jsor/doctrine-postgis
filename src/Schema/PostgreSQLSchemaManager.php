<?php

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager as BasePostgreSQLSchemaManager;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

class PostgreSQLSchemaManager extends BasePostgreSQLSchemaManager
{
    /**
     * @param array $tableColumn
     * @return Column
     * @throws Exception
     */
    protected function _getPortableTableColumnDefinition(array $tableColumn): Column
    {
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        if (strtolower($tableColumn['type']) === 'geometry') {
            $this->registerPostGISTypes();

            $geometryType = null;
            $srid         = null;

            if (isset($tableColumn['complete_type'])) {
                if (preg_match('/geometry\(([^,]+),\s*(\d+)\)/i', $tableColumn['complete_type'], $matches)) {
                    $geometryType = strtoupper($matches[1]);
                    $srid         = (int)$matches[2];
                }
            }

            $column = new Column(
                $tableColumn['field'],
                Type::getType(PostGISType::GEOMETRY)
            );

            if (isset($tableColumn['default'])) {
                $column->setDefault($tableColumn['default']);
            }

            $column->setNotnull(isset($tableColumn['isnotnull']) ? (bool)$tableColumn['isnotnull'] : false);

            if (isset($tableColumn['comment'])) {
                $column->setComment($tableColumn['comment']);
            }

            if ($geometryType !== null) {
                $column->setPlatformOption('geometry_type', $geometryType);
            }

            if ($srid !== null) {
                $column->setPlatformOption('srid', $srid);
            }

            return $column;
        }

        return parent::_getPortableTableColumnDefinition($tableColumn);
    }

    /**
     * Get PostGIS column information from the database
     */
    private function getPostGISColumnInfo(string $table, string $column): ?array
    {
        try {
            // Improved regex pattern for extracting geometry type and SRID
            $sql = "
                SELECT 
                    format_type(a.atttypid, a.atttypmod) as full_type,
                    CASE 
                        WHEN format_type(a.atttypid, a.atttypmod) ~ 'geometry\\(([^,]+),\\s*(\\d+)\\)' 
                        THEN (regexp_matches(format_type(a.atttypid, a.atttypmod), 'geometry\\(([^,]+),\\s*(\\d+)\\)'))[1]
                    END as geometry_type,
                    CASE 
                        WHEN format_type(a.atttypid, a.atttypmod) ~ 'geometry\\(([^,]+),\\s*(\\d+)\\)' 
                        THEN (regexp_matches(format_type(a.atttypid, a.atttypmod), 'geometry\\(([^,]+),\\s*(\\d+)\\)'))[2]::integer
                    END as srid
                FROM pg_attribute a
                JOIN pg_class t ON a.attrelid = t.oid
                JOIN pg_namespace n ON t.relnamespace = n.oid
                WHERE t.relname = ? 
                AND a.attname = ?
                AND NOT a.attisdropped";

            $columnInfo = $this->_conn->fetchAssociative($sql, [
                $this->trimQuotes($table),
                $this->trimQuotes($column),
            ]);

            if ($columnInfo && $columnInfo['geometry_type'] !== null) {
                return [
                    'geometry_type' => strtoupper($columnInfo['geometry_type']),
                    'srid'          => (int)$columnInfo['srid'],
                ];
            }

            // Try fallback to geometry_columns/geography_columns
            $schemaManager = new SchemaManager($this->_conn);

            $spatialInfo = $schemaManager->getGeometrySpatialColumnInfo($table, $column);
            if ($spatialInfo) {
                return $spatialInfo;
            }

            $spatialInfo = $schemaManager->getGeographySpatialColumnInfo($table, $column);
            if ($spatialInfo) {
                return $spatialInfo;
            }
        } catch (\Exception $e) {
            // Log error but continue
            error_log('[PostGIS] Error fetching column info: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Register PostGIS types if not already registered
     */
    private function registerPostGISTypes(): void
    {
        if (!Type::hasType(PostGISType::GEOMETRY)) {
            Type::addType(PostGISType::GEOMETRY, GeometryType::class);
        }

        if (!Type::hasType(PostGISType::GEOGRAPHY)) {
            Type::addType(PostGISType::GEOGRAPHY, GeographyType::class);
        }
    }

    /**
     * Helper method to trim quotes from identifiers
     */
    private function trimQuotes(string $identifier): string
    {
        return str_replace(['`', '"', '[', ']'], '', $identifier);
    }
}
