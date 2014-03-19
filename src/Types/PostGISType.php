<?php

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class PostGISType extends Type
{
    const GEOMETRY = 'geometry';
    const GEOGRAPHY = 'geography';

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return array($this->getName());
    }

    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return sprintf('ST_AsEWKT(%s)', $sqlExpr);
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('ST_GeomFromText(%s)', $sqlExpr);
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return sprintf(
            '%s(%s)',
            $this->getName(),
            implode(', ', $this->getNormalizedSpatialOptions($fieldDeclaration))
        );
    }

    /**
     * @param array $options
     * @return mixed
     */
    abstract public function getNormalizedSpatialOptions(array $options = array());
}
