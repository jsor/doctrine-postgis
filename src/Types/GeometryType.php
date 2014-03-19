<?php

namespace Jsor\Doctrine\PostGIS\Types;

class GeometryType extends PostGISType
{
    public function getName()
    {
        return PostGISType::GEOMETRY;
    }

    public function getNormalizedSpatialOptions(array $options = array())
    {
        return array(
            'spatial_type' => strtoupper(isset($options['spatial_type']) ? $options['spatial_type'] : 'GEOMETRY'),
            'spatial_srid' => isset($options['spatial_srid']) ? $options['spatial_srid'] : 0
        );
    }
}
