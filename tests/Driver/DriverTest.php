<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver as PgSQLDriver;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jsor\Doctrine\PostGIS\Driver\Driver
 *
 * @runTestsInSeparateProcesses
 *
 * @internal
 */
final class DriverTest extends TestCase
{
    public function testConnect(): void
    {
        $driver = $this->getDriver();
        $conn = $driver->connect([
            'driver' => getenv('DB_TYPE'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
        ]);

        static::assertInstanceOf(Connection::class, $conn);
    }

    public function testGetDatabasePlatform(): void
    {
        $driver = $this->getDriver();

        static::assertInstanceOf(PostGISPlatform::class, $driver->getDatabasePlatform());
    }

    public function providerVersions(): iterable
    {
        yield ['14'];
        yield ['15'];
        yield ['16'];
        yield ['17'];
        yield ['18'];
    }

    /** @dataProvider providerVersions */
    public function testCreateDatabasePlatformForVersion(string $version): void
    {
        $driver = $this->getDriver();

        static::assertInstanceOf(PostGISPlatform::class, $driver->createDatabasePlatformForVersion($version));
    }

    public function testGetExceptionConverter(): void
    {
        $driver = $this->getDriver();

        static::assertInstanceOf(ExceptionConverter::class, $driver->getExceptionConverter());
    }

    private function getDriver(): Driver
    {
        return new Driver(new PgSQLDriver());
    }
}
