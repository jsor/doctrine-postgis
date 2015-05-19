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
            'geometry_type' => strtoupper(isset($options['geometry_type']) ? $options['geometry_type'] : 'GEOMETRY'),
            'srid' => isset($options['srid']) ? $options['srid'] : 0,
        );
    }
}
