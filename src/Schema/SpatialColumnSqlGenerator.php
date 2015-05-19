<?php

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Table;

class SpatialColumnSqlGenerator
{
    private $platform;

    public function __construct(PostgreSqlPlatform $platform)
    {
        $this->platform = $platform;
    }

    public function getSql(Column $column, $table)
    {
        if (!$table instanceof Table) {
            $table = new Identifier($table);
        }

        $sql = array();

        $normalized = $column->getType()->getNormalizedPostGISColumnOptions(
            $column->getCustomSchemaOptions()
        );

        $srid = $normalized['srid'];

        // PostGIS 1.5 uses -1 for undefined SRID's
        if ($srid <= 0) {
            $srid = -1;
        }

        $type = strtoupper($normalized['geometry_type']);

        if ('ZM' === substr($type, -2)) {
            $dimension = 4;
            $type = substr($type, 0, -2);
        } elseif ('M' === substr($type, -1)) {
            $dimension = 3;
        } elseif ('Z' === substr($type, -1)) {
            $dimension = 3;
            $type = substr($type, 0, -1);
        } else {
            $dimension = 2;
        }

        // Geometry columns are created by the AddGeometryColumn stored procedure
        $sql[] = sprintf(
            "SELECT AddGeometryColumn('%s', '%s', %d, '%s', %d)",
            $table->getName(),
            $column->getName(),
            $srid,
            $type,
            $dimension
        );

        if ($column->getNotnull()) {
            // Add a NOT NULL constraint to the field
            $sql[] = sprintf(
                'ALTER TABLE %s ALTER %s SET NOT NULL',
                $table->getQuotedName($this->platform),
                $column->getQuotedName($this->platform)
            );
        }

        return $sql;
    }
}
