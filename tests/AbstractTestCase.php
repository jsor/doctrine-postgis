<?php

namespace Jsor\Doctrine\PostGIS;

use Doctrine\DBAL\Types\Type;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    protected function _registerTypes()
    {
        if (!Type::hasType('geometry')) {
            Type::addType('geometry', 'Jsor\Doctrine\PostGIS\Types\GeometryType');
        }

        if (!Type::hasType('geography')) {
            Type::addType('geography', 'Jsor\Doctrine\PostGIS\Types\GeographyType');
        }
    }
}
