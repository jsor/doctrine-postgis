<?php

namespace Jsor\Doctrine\PostGIS\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

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
        // ::geometry type cast needed for 1.5
        return sprintf('ST_AsEWKT(%s::geometry)', $sqlExpr);
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('ST_GeomFromText(%s)', $sqlExpr);
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $options = $this->getNormalizedPostGISColumnOptions($fieldDeclaration);

        return sprintf(
            '%s(%s, %d)',
            $this->getName(),
            $options['geometry_type'],
            $options['srid']
        );
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    abstract public function getNormalizedPostGISColumnOptions(array $options = array());
}
