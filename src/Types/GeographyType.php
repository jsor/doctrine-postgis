<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class GeographyType extends PostGISType
{
    public function getName(): string
    {
        return PostGISType::GEOGRAPHY;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform): string
    {
        return sprintf('ST_GeographyFromText(%s)', $sqlExpr);
    }

    public function getNormalizedPostGISColumnOptions(array $options = []): array
    {
        $srid = isset($options['srid']) ? (int) $options['srid'] : 4326;

        if (0 === $srid) {
            $srid = 4326;
        }

        return [
            'geometry_type' => isset($options['geometry_type']) ? strtoupper($options['geometry_type']) : 'GEOMETRY',
            'srid' => $srid,
        ];
    }
}
