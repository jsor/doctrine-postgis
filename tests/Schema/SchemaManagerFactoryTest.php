<?php

declare(strict_types=1);

namespace Schema;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver;
use Jsor\Doctrine\PostGIS\Schema\SchemaManager;
use Jsor\Doctrine\PostGIS\Schema\SchemaManagerFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jsor\Doctrine\PostGIS\Schema\SchemaManagerFactory
 *
 * @internal
 */
final class SchemaManagerFactoryTest extends TestCase
{
    public function testCreateSchemaManager(): void
    {
        $factory = new SchemaManagerFactory();
        $params = [
            'driver' => getenv('DB_TYPE'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
        ];

        static::assertInstanceOf(SchemaManager::class, $factory->createSchemaManager(new Connection($params, new Driver())));
    }
}
