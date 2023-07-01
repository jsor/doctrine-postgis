<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

final class GeoJsonType extends GeographyType
{
    public const NAME = 'geojson';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        return sprintf('ST_AsGeoJSON(%s)', $sqlExpr);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return $value;
        }

        return $value;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform): string
    {
        return sprintf('ST_GeomFromGeoJSON(%s)::geography', $sqlExpr);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return parent::convertToDatabaseValue(json_encode($value), $platform);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        /** @var array{geometry_type?: string|null, srid?: int|string|null} $column */
        $options = $this->getNormalizedPostGISColumnOptions($column);

        return sprintf(
            '%s(%s, %d)',
            $this->getName(),
            $options['geometry_type'],
            $options['srid']
        );
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
