<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

class GeometryType extends PostGISType
{
    public function getName(): string
    {
        return PostGISType::GEOMETRY;
    }

    public function getNormalizedPostGISColumnOptions(array $options = []): array
    {
        return [
            'geometry_type' => isset($options['geometry_type']) ? strtoupper($options['geometry_type']) : 'GEOMETRY',
            'srid' => isset($options['srid']) ? (int) $options['srid'] : 0,
        ];
    }
}
