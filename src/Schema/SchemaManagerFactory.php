<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema;

final class SchemaManagerFactory implements Schema\SchemaManagerFactory
{
    public function createSchemaManager(Connection $connection): Schema\AbstractSchemaManager
    {
        /** @var PostgreSQLPlatform $platform */
        $platform = $connection->getDatabasePlatform();

        return new SchemaManager($connection, $platform);
    }
}
