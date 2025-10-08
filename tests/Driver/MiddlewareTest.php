<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Driver\PgSQL\Driver as PgSQLDriver;
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
    }
}
