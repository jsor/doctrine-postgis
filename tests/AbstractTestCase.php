<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function _registerTypes(): void
    {
        if (!Type::hasType('geometry')) {
            Type::addType('geometry', 'Jsor\Doctrine\PostGIS\Types\GeometryType');
        }

        if (!Type::hasType('geography')) {
            Type::addType('geography', 'Jsor\Doctrine\PostGIS\Types\GeographyType');
        }
    }

    protected function getPlatformMock()
    {
        $platform = $this->getMockForAbstractClass(AbstractPlatform::class);

        $platform
            ->method('getName')
            ->willReturn('postgresql');

        return $platform;
    }
}
