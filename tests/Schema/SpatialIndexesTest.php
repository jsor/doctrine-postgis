<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractTestCase;

/**
 * @covers \Jsor\Doctrine\PostGIS\Schema\SpatialIndexes
 *
 * @internal
 */
final class SpatialIndexesTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        static::_registerTypes();
    }

    public function providerFilterTableDiff(): iterable
    {
        $baseTable = new Table('points');
        $baseTable->addColumn('name', 'string', ['length' => 42]);
        $baseTable->addColumn('point', 'geometry', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 3785]]);
        $baseTable->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);

        $table = clone $baseTable;
        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->addedIndexes[] = new Index('name_idx', ['name']);
        $tableDiff->addedIndexes[] = new Index('linestring_idx', ['linestring'], false, false, ['spatial']);

        yield 'Added spatial' => [$tableDiff, 1, 0];

        $table = clone $baseTable;
        $table->addIndex(['name'], 'name_idx', []);
        $table->addIndex(['point'], 'point_idx', []);

        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedIndexes[] = new Index('name_idx', ['name']);
        $tableDiff->changedIndexes[] = new Index('point_idx', ['point'], false, false, ['spatial']);

        yield 'Changed spatial' => [$tableDiff, 0, 1];
    }

    /** @dataProvider providerFilterTableDiff */
    public function testFilterTableDiff(TableDiff $tableDiff, int $addedIndexes, int $changedIndexes): void
    {
        SpatialIndexes::filterTableDiff($tableDiff);

        static::assertCount($addedIndexes, $tableDiff->addedIndexes);
        static::assertCount($changedIndexes, $tableDiff->changedIndexes);
    }

    public function providerEnsureTableFlag(): iterable
    {
        $baseTable = new Table('points');
        $baseTable->addColumn('name', 'string', ['length' => 42]);
        $baseTable->addIndex(['name'], 'name_idx', []);
        $baseTable->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);

        $table = clone $baseTable;
        $table->addIndex(['linestring'], 'linestring_idx', ['spatial']);

        yield 'With spatial flag' => [$table];

        $table = clone $baseTable;
        $table->addIndex(['linestring'], 'linestring_idx', []);

        yield 'Without spatial flag' => [$table];
    }

    /** @dataProvider providerEnsureTableFlag */
    public function testEnsureTableFlag(Table $table): void
    {
        $spatialIndexes = SpatialIndexes::ensureTableFlag($table);

        static::assertCount(1, $spatialIndexes);
        static::assertSame('linestring_idx', $spatialIndexes[0]->getName());
        static::assertTrue($spatialIndexes[0]->hasFlag('spatial'));
    }

    public function providerEnsureTableDiffs(): iterable
    {
        static::_registerTypes();

        $table = new Table('points');
        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->addedColumns[] = (new Column('linestring', Type::getType('geometry')))->setPlatformOptions(['geometry_type' => 'linestring', 'srid' => 3785]);
        $tableDiff->addedIndexes[] = new Index('linestring_idx', ['linestring'], false, false, []);

        yield 'Added column and index' => [0, $tableDiff];

        $table = new Table('points');
        $table->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $table->addIndex(['linestring'], 'linestring_idx', []);
        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedIndexes[] = new Index('linestring_idx', ['linestring'], false, false, ['spatial']);

        yield 'Changed index' => [1, $tableDiff];

        $tableDiff = new TableDiff('points');
        $tableDiff->addedIndexes[] = new Index('linestring_idx', ['linestring'], false, false, ['spatial']);
        $tableDiff->changedIndexes[] = new Index('point_idx', ['point'], false, false, ['spatial']);

        yield 'Empty fromTable is skipped' => [0, $tableDiff];
    }

    /** @dataProvider providerEnsureTableDiffs */
    public function testEnsureTableDiffFlag(int $expected, TableDiff $tableDiff): void
    {
        $spatialIndexes = SpatialIndexes::ensureTableDiffFlag($tableDiff);

        static::assertCount($expected, $spatialIndexes);

        foreach ($spatialIndexes as $spatialIndex) {
            static::assertTrue($spatialIndex->hasFlag('spatial'));
        }
    }
}
