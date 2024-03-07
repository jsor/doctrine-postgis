<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Jsor\Doctrine\PostGIS\AbstractTestCase;
use Jsor\Doctrine\PostGIS\Driver\PostGISPlatform;

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
        $comparator = new Comparator(new PostGISPlatform());

        $makeTable = static function (): Table {
            $table = new Table('points');
            $table->addColumn('name', 'string', ['length' => 42]);
            $table->addColumn('point', 'geometry', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 3785]]);
            $table->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);

            return $table;
        };

        $fromTable = $makeTable();
        $toTable = $makeTable();
        $toTable->addIndex(['name'], 'name_idx');
        $toTable->addIndex(['linestring'], 'linestring_idx', ['spatial']);

        yield 'Added spatial' => [$comparator->compareTables($fromTable, $toTable), 1, 0];

        $fromTable = $makeTable();
        $fromTable->addIndex(['name'], 'name_idx', []);
        $fromTable->addIndex(['point'], 'point_idx', []);

        $toTable = $makeTable();
        $toTable->addIndex(['name'], 'name_idx');
        $toTable->addIndex(['point', 'linestring'], 'point_idx');
        $toTable->addIndex(['linestring'], 'linestring_idx', ['spatial']);

        yield 'Changed spatial' => [$comparator->compareTables($fromTable, $toTable), 0, 1];
    }

    /** @dataProvider providerFilterTableDiff */
    public function testFilterTableDiff(TableDiff $tableDiff, int $addedIndexes, int $modifiedIndexes): void
    {
        $tableDiff = SpatialIndexes::filterTableDiff($tableDiff);

        static::assertCount($addedIndexes, $tableDiff->getAddedIndexes(), 'Incorrect added index count');
        static::assertCount($modifiedIndexes, $tableDiff->getModifiedIndexes(), 'Incorrect modified index count');
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
        SpatialIndexes::ensureSpatialIndexFlags($table);
        $spatialIndexes = SpatialIndexes::extractSpatialIndicies($table->getIndexes());

        static::assertCount(1, $spatialIndexes);
        static::assertSame('linestring_idx', $spatialIndexes['linestring_idx']->getName());
        static::assertTrue($spatialIndexes['linestring_idx']->hasFlag('spatial'));
    }

    public function providerEnsureSpatialIndexFlags(): iterable
    {
        static::_registerTypes();

        $comparator = new Comparator(new PostGISPlatform());

        $fromTable = new Table('points');

        $toTable = new Table('points');
        $toTable->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $toTable->addIndex(['linestring'], 'linestring_idx', ['spatial']);

        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        yield 'Added column and index' => [1, $tableDiff];

        $fromTable = new Table('points');
        $fromTable->addColumn('linestring_1', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $fromTable->addColumn('linestring_2', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $fromTable->addIndex(['linestring_1'], 'linestring_idx', []);

        $toTable = new Table('points');
        $toTable->addColumn('linestring_1', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $toTable->addColumn('linestring_2', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $toTable->addIndex(['linestring_1', 'linestring_2'], 'linestring_idx', ['spatial']);

        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        yield 'Changed index' => [1, $tableDiff];
    }

    /** @dataProvider providerEnsureSpatialIndexFlags */
    public function testEnsureSpatialIndexFlags(int $expected, TableDiff $tableDiff): void
    {
        SpatialIndexes::ensureSpatialIndexFlags($tableDiff);
        $spatialIndexes = array_merge(
            SpatialIndexes::extractSpatialIndicies($tableDiff->getAddedIndexes()),
            SpatialIndexes::extractSpatialIndicies($tableDiff->getModifiedIndexes()),
        );

        static::assertCount($expected, $spatialIndexes);

        foreach ($spatialIndexes as $spatialIndex) {
            static::assertTrue($spatialIndex->hasFlag('spatial'), 'Missing spatial flag');
        }
    }
}
