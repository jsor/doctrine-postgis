<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver as PgSQLDriver;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
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
        static::assertFalse(Type::hasType(PostGISType::GEOGRAPHY));
        static::assertFalse(Type::hasType(PostGISType::GEOMETRY));

        $driver = $this->getDriver();
        $driver->connect([
            'driver' => getenv('DB_TYPE'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
        ]);

        static::assertTrue(Type::hasType(PostGISType::GEOGRAPHY));
        static::assertTrue(Type::hasType(PostGISType::GEOMETRY));
    }

    public function testGetDatabasePlatform(): void
    {
        $driver = $this->getDriver();

        static::assertInstanceOf(PostGISPlatform::class, $driver->getDatabasePlatform());
    }

    public function providerVersions(): iterable
    {
        yield ['11'];
        yield ['12'];
        yield ['13'];
        yield ['14'];
        yield ['15'];
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
