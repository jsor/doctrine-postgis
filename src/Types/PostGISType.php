<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class PostGISType extends Type
{
    public const GEOMETRY = 'geometry';
    public const GEOGRAPHY = 'geography';

    public function canRequireSQLConversion(): bool
    {
        return true;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [$this->getName()];
    }

    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        return sprintf('ST_AsEWKT(%s)', $sqlExpr);
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform): string
    {
        return sprintf('ST_GeomFromEWKT(%s)', $sqlExpr);
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

    /**
     * @param array{geometry_type?: string|null, srid?: int|string|null} $options
     *
     * @return array{geometry_type: string, srid: int}
     */
    abstract public function getNormalizedPostGISColumnOptions(array $options = []): array;
}
