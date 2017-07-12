<?php

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class GeographyType extends PostGISType
{
    public function getName()
    {
        return PostGISType::GEOGRAPHY;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('ST_GeographyFromText(%s)', $sqlExpr);
    }

    public function getNormalizedPostGISColumnOptions(array $options = array())
    {
        $srid = isset($options['srid']) ? (int) $options['srid'] : 4326;

        if (0 === $srid) {
            $srid = 4326;
        }

        return array(
            'geometry_type' => isset($options['geometry_type']) ? strtoupper($options['geometry_type']) : 'GEOMETRY',
            'srid' => $srid,
        );
    }
}
