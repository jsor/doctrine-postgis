<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Note: This type is not suited to be used in entity mappings.
 * It just prevents "Unknown database type..." exceptions thrown during database
 * inspections by the schema tool.
 */
class RasterType extends Type
{
    public function getName(): string
    {
        return 'raster';
    }

    public function canRequireSQLConversion(): bool
    {
        return true;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [$this->getName()];
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform): string
    {
        return sprintf('%s::raster', $sqlExpr);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $this->getName();
    }
}
