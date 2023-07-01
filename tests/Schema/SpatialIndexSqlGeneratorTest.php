<?php

declare(strict_types=1);

namespace Schema;

use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;
use Jsor\Doctrine\PostGIS\AbstractTestCase;
use Jsor\Doctrine\PostGIS\Driver\PostGISPlatform;
use Jsor\Doctrine\PostGIS\Schema\SpatialIndexSqlGenerator;

/**
 * @covers \Jsor\Doctrine\PostGIS\Schema\SpatialIndexSqlGenerator
 *
 * @internal
 */
final class SpatialIndexSqlGeneratorTest extends AbstractTestCase
{
    public function providerGetSql(): iterable
    {
        static::_registerTypes();

        $table = new Table('points');
        $table->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        yield 'Spatial index' => [
            new Index('linestring_idx', ['linestring'], false, false, ['spatial']),
            $table,
            'CREATE INDEX linestring_idx ON points USING gist(linestring)',
        ];

        yield 'Primary index' => [
            new Index('linestring_idx', ['linestring'], false, true, ['spatial']),
            $table,
            'ALTER TABLE points ADD PRIMARY KEY (linestring)',
        ];
    }

    /** @dataProvider providerGetSql */
    public function testGetSql(Index $index, Table|Identifier $table, string $expected): void
    {
        $generator = new SpatialIndexSqlGenerator(new PostGISPlatform());

        static::assertSame($expected, $generator->getSql($index, $table));
    }

    public function testGetSqlThrowExceptionEmptyColumns(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Incomplete definition. 'columns' required");

        $generator = new SpatialIndexSqlGenerator(new PostGISPlatform());
        $table = new Table('points');
        $table->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $index = new Index('linestring_idx', [], false, false, ['spatial']);

        $generator->getSql($index, $table);
    }
}
