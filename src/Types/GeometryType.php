<?php

namespace Jsor\Doctrine\PostGIS\Types;

class GeometryType extends PostGISType
{
    public function getName()
    {
        return PostGISType::GEOMETRY;
    }

    public function getNormalizedPostGISColumnOptions(array $options = array())
    {
        return array(
            'geometry_type' => isset($options['geometry_type']) ? strtoupper($options['geometry_type']) : 'GEOMETRY',
            'srid' => isset($options['srid']) ? (int) $options['srid'] : 0,
        );
    }
}
