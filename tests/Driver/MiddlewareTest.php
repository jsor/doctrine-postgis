<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Driver\PgSQL\Driver as PgSQLDriver;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jsor\Doctrine\PostGIS\Driver\Middleware
 *
 * @internal
 */
final class MiddlewareTest extends TestCase
{
    public function testWrap(): void
    {
        $middleware = new Middleware();

        static::assertInstanceOf(Driver::class, $middleware->wrap(new PgSQLDriver()));
        static::assertTrue(Type::hasType(PostGISType::GEOGRAPHY));
        static::assertTrue(Type::hasType(PostGISType::GEOMETRY));
    }
}
