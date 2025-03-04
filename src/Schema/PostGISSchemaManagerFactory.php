<?php

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\SchemaManagerFactory;

class PostGISSchemaManagerFactory implements SchemaManagerFactory
{
    /**
     * @throws Exception
     */
    public function createSchemaManager(Connection $connection): AbstractSchemaManager
    {
        $platform = $connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            return new PostgreSQLSchemaManager($connection, $platform);
        }

        return $connection->createSchemaManager();
    }
}
