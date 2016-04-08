<?php

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
    public function getName()
    {
        return 'raster';
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return array($this->getName());
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('%s::raster', $sqlExpr);
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $this->getName();
    }
}
