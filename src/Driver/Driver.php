<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL;
use Doctrine\DBAL\Connection\StaticServerVersionProvider;
use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\ServerVersionProvider;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
use Jsor\Doctrine\PostGIS\Utils\Doctrine;

final class Driver extends AbstractPostgreSQLDriver
{
    private DBAL\Driver $decorated;

    public function __construct(DBAL\Driver $decorated)
    {
        $this->decorated = $decorated;
    }

    public function connect(array $params): DBAL\Driver\Connection
    {
        $connection = $this->decorated->connect($params);
        if (!Type::hasType(PostGISType::GEOMETRY)) {
            Type::addType(PostGISType::GEOMETRY, GeometryType::class);
        }

        if (!Type::hasType(PostGISType::GEOGRAPHY)) {
            Type::addType(PostGISType::GEOGRAPHY, GeographyType::class);
        }

        return $connection;
    }

    public function getDatabasePlatform(?ServerVersionProvider $versionProvider = null): PostgreSQLPlatform
    {
        return new PostGISPlatform();
    }

    /**
     * @param string $version
     */
    public function createDatabasePlatformForVersion($version): AbstractPlatform
    {
        // Remove when DBAL v3 support is dropped.
        if (Doctrine::isV3()) {
            return $this->getDatabasePlatform();
        }

        return $this->getDatabasePlatform(new StaticServerVersionProvider($version));
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return $this->decorated->getExceptionConverter();
    }
}
