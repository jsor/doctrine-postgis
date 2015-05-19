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

    public function getNormalizedSpatialOptions(array $options = array())
    {
        $srid = isset($options['srid']) ? $options['srid'] : 4326;

        if (0 === $srid) {
            $srid = 4326;
        }

        return array(
            'geometry_type' => strtoupper(isset($options['geometry_type']) ? $options['geometry_type'] : 'GEOMETRY'),
            'srid' => $srid,
        );
    }
}
