<?php

namespace Jsor\Doctrine\PostGIS;

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function _registerTypes()
    {
        if (!Type::hasType('geometry')) {
            Type::addType('geometry', 'Jsor\Doctrine\PostGIS\Types\GeometryType');
        }

        if (!Type::hasType('geography')) {
            Type::addType('geography', 'Jsor\Doctrine\PostGIS\Types\GeographyType');
        }

        if (!Type::hasType('raster')) {
            Type::addType('raster', 'Jsor\Doctrine\PostGIS\Types\RasterType');
        }
    }

    protected function getPlatformMock()
    {
        $platform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');

        $platform
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('postgresql'));

        return $platform;
    }
}
