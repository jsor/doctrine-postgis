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
        $srid = isset($options['spatial_srid']) ? $options['spatial_srid'] : 4326;

        if (0 === $srid) {
            $srid = 4326;
        }

        return array(
            'spatial_type' => strtoupper(isset($options['spatial_type']) ? $options['spatial_type'] : 'GEOMETRY'),
            'spatial_srid' => $srid,
        );
    }
}
