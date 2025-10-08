<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

final class Middleware implements DBAL\Driver\Middleware
{
    public function wrap(DBAL\Driver $driver): DBAL\Driver
    {
        // Register PostGIS types once at middleware level (global registry)
        // This is done here rather than in connect() to avoid repeated registration
        if (!Type::hasType(PostGISType::GEOMETRY)) {
            Type::addType(PostGISType::GEOMETRY, GeometryType::class);
        }

        if (!Type::hasType(PostGISType::GEOGRAPHY)) {
            Type::addType(PostGISType::GEOGRAPHY, GeographyType::class);
        }

        return new Driver($driver);
    }
}
