<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function _registerTypes(): void
    {
        if (!Type::hasType('geometry')) {
            Type::addType('geometry', GeometryType::class);
        }

        if (!Type::hasType('geography')) {
            Type::addType('geography', GeographyType::class);
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
