<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Driver\PostGISPlatform;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use RuntimeException;

/**
 * @covers \Jsor\Doctrine\PostGIS\Schema\SchemaManager
 *
 * @internal
 */
final class SchemaManagerTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->_execFile('points_drop.sql');
        $this->_execFile('points_create.sql');

        $this->_execFile('reserved-words_drop.sql');
        $this->_execFile('reserved-words_create.sql');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->_execFile('points_drop.sql');

        $this->_execFile('reserved-words_drop.sql');
    }

    public function testListSpatialIndexes(): void
    {
        $schemaManager = $this->getSchemaManager();

        $expected = [
            'idx_27ba8e29b7a5f324' => [
                0 => 'point',
            ],
            'idx_27ba8e2999674a3d' => [
                0 => 'point_2d',
            ],
            'idx_27ba8e293be136c3' => [
                0 => 'point_3dz',
            ],
            'idx_27ba8e29b832b304' => [
                0 => 'point_3dm',
            ],
            'idx_27ba8e29cf3dedbb' => [
                0 => 'point_4d',
            ],
            'idx_27ba8e293c257075' => [
                0 => 'point_2d_nullable',
            ],
            'idx_27ba8e293d5fe69e' => [
                0 => 'point_2d_nosrid',
            ],
            'idx_27ba8e295f51a43c' => [
                0 => 'point_geography_2d',
            ],
            'idx_27ba8e295afbb72d' => [
                0 => 'point_geography_2d_srid',
            ],
        ];

        $this->assertEquals($expected, $schemaManager->listSpatialIndexes('foo.points'));
    }

    public function testGetGeometrySpatialColumnInfo(): void
    {
        $schemaManager = $this->getSchemaManager();

        $this->assertNull($schemaManager->getGeometrySpatialColumnInfo('foo.points', 'text'));

        $expected = [
            'type' => 'GEOMETRY',
            'srid' => 0,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'geometry'));

        $expected = [
            'type' => 'POINT',
            'srid' => 0,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point'));

        $expected = [
            'type' => 'POINT',
            'srid' => 3785,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_2d'));

        $expected = [
            'type' => 'POINTZ',
            'srid' => 3785,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_3dz'));

        $expected = [
            'type' => 'POINTM',
            'srid' => 3785,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_3dm'));

        $expected = [
            'type' => 'POINTZM',
            'srid' => 3785,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_4d'));

        $expected = [
            'type' => 'POINT',
            'srid' => 3785,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_2d_nullable'));

        $expected = [
            'type' => 'POINT',
            'srid' => 0,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_2d_nosrid'));
    }

    public function testGetGeographySpatialColumnInfo(): void
    {
        $schemaManager = $this->getSchemaManager();

        $this->assertNull($schemaManager->getGeographySpatialColumnInfo('foo.points', 'text'));

        $expected = [
            'type' => 'GEOMETRY',
            'srid' => 4326,
        ];
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('points', 'geography'));

        $expected = [
            'type' => 'POINT',
            'srid' => 4326,
        ];
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('points', 'point_geography_2d'));

        $expected = [
            'type' => 'POINT',
            'srid' => 4326,
        ];
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('points', 'point_geography_2d_srid'));
    }

    public function testGetGeometrySpatialColumnInfoWithReservedWords(): void
    {
        $schemaManager = $this->getSchemaManager();

        $expected = [
            'type' => 'GEOMETRY',
            'srid' => 0,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('"user"', '"user"'));
    }

    public function testGetGeographySpatialColumnInfoWithReservedWords(): void
    {
        $schemaManager = $this->getSchemaManager();

        $expected = [
            'type' => 'GEOMETRY',
            'srid' => 4326,
        ];
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('"user"', '"primary"'));
    }

    public function testAlterTableThrowsExceptionForChangedType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The type of a spatial column cannot be changed (Requested changing type from "geometry" to "geography" for column "point_2d" in table "points")');

        $schemaManager = $this->getSchemaManager();
        $comparator = $schemaManager->createComparator();

        $fromTable = $schemaManager->introspectTable('points');
        $toTable = clone $fromTable;
        $toTable->modifyColumn('point_2d', ['type' => Type::getType('geography')]);
        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $schemaManager->alterTable($tableDiff);
    }

    public function testAlterTableThrowsExceptionForChangedSpatialType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The geometry_type of a spatial column cannot be changed (Requested changing type from "POINT" to "LINESTRING" for column "point_2d" in table "points")');

        $schemaManager = $this->getSchemaManager();
        $comparator = $schemaManager->createComparator();

        $fromTable = $schemaManager->introspectTable('points');
        $toTable = clone $fromTable;
        $toTable->modifyColumn('point_2d', ['platformOptions' => ['geometry_type' => 'LINESTRING']]);
        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $schemaManager->alterTable($tableDiff);
    }

    public function testListTableColumnsPoint(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point', $columns);
        $this->assertEquals('point', strtolower($columns['point']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point']->getType());
        $this->assertFalse($columns['point']->getUnsigned());
        $this->assertTrue($columns['point']->getNotnull());
        $this->assertNull($columns['point']->getDefault());
        $this->assertIsArray($columns['point']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point']->getPlatformOption('geometry_type'));
        $this->assertEquals(0, $columns['point']->getPlatformOption('srid'));
    }

    public function testListTableColumnsPoint2d(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_2d', $columns);
        $this->assertEquals('point_2d', strtolower($columns['point_2d']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_2d']->getType());
        $this->assertFalse($columns['point_2d']->getUnsigned());
        $this->assertTrue($columns['point_2d']->getNotnull());
        $this->assertNull($columns['point_2d']->getDefault());
        $this->assertIsArray($columns['point_2d']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_2d']->getPlatformOption('srid'));
    }

    public function testListTableColumns3dz(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_3dz', $columns);
        $this->assertEquals('point_3dz', strtolower($columns['point_3dz']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_3dz']->getType());
        $this->assertFalse($columns['point_3dz']->getUnsigned());
        $this->assertTrue($columns['point_3dz']->getNotnull());
        $this->assertNull($columns['point_3dz']->getDefault());
        $this->assertIsArray($columns['point_3dz']->getPlatformOptions());

        $this->assertEquals('POINTZ', $columns['point_3dz']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_3dz']->getPlatformOption('srid'));
    }

    public function testListTableColumns3dm(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_3dm', $columns);
        $this->assertEquals('point_3dm', strtolower($columns['point_3dm']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_3dm']->getType());
        $this->assertFalse($columns['point_3dm']->getUnsigned());
        $this->assertTrue($columns['point_3dm']->getNotnull());
        $this->assertNull($columns['point_3dm']->getDefault());
        $this->assertIsArray($columns['point_3dm']->getPlatformOptions());

        $this->assertEquals('POINTM', $columns['point_3dm']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_3dm']->getPlatformOption('srid'));
    }

    public function testListTableColumns4D(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_2d_nosrid', $columns);
        $this->assertEquals('point_4d', strtolower($columns['point_4d']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_4d']->getType());
        $this->assertFalse($columns['point_4d']->getUnsigned());
        $this->assertTrue($columns['point_4d']->getNotnull());
        $this->assertNull($columns['point_4d']->getDefault());
        $this->assertIsArray($columns['point_4d']->getPlatformOptions());

        $this->assertEquals('POINTZM', $columns['point_4d']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_4d']->getPlatformOption('srid'));
    }

    public function testListTableColumns2dNullable(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_2d_nullable', $columns);
        $this->assertEquals('point_2d_nullable', strtolower($columns['point_2d_nullable']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_2d_nullable']->getType());
        $this->assertFalse($columns['point_2d_nullable']->getUnsigned());
        $this->assertFalse($columns['point_2d_nullable']->getNotnull());
        // $this->assertEquals('NULL::geometry', $columns['point_2d_nullable']->getDefault());
        $this->assertIsArray($columns['point_2d_nullable']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d_nullable']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_2d_nullable']->getPlatformOption('srid'));
    }

    public function testListTableColumns2dNoSrid(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_2d_nosrid', $columns);
        $this->assertEquals('point_2d_nosrid', strtolower($columns['point_2d_nosrid']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_2d_nosrid']->getType());
        $this->assertFalse($columns['point_2d_nosrid']->getUnsigned());
        $this->assertTrue($columns['point_2d_nosrid']->getNotnull());
        $this->assertNull($columns['point_2d_nosrid']->getDefault());
        $this->assertIsArray($columns['point_2d_nosrid']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d_nosrid']->getPlatformOption('geometry_type'));
        $this->assertEquals(0, $columns['point_2d_nosrid']->getPlatformOption('srid'));
    }

    public function testListTableColumnsGeography2d(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_geography_2d', $columns);
        $this->assertEquals('point_geography_2d', strtolower($columns['point_geography_2d']->getName()));
        $this->assertInstanceOf(GeographyType::class, $columns['point_geography_2d']->getType());
        $this->assertFalse($columns['point_geography_2d']->getUnsigned());
        $this->assertTrue($columns['point_geography_2d']->getNotnull());
        $this->assertNull($columns['point_geography_2d']->getDefault());
        $this->assertIsArray($columns['point_geography_2d']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_geography_2d']->getPlatformOption('geometry_type'));
        $this->assertEquals(4326, $columns['point_geography_2d']->getPlatformOption('srid'));
    }

    public function testListTableColumnsGeography2dSrid(): void
    {
        $columns = $this->getSchemaManager()->listTableColumns('points');

        $this->assertArrayHasKey('point_geography_2d_srid', $columns);
        $this->assertEquals('point_geography_2d_srid', strtolower($columns['point_geography_2d_srid']->getName()));
        $this->assertInstanceOf(GeographyType::class, $columns['point_geography_2d_srid']->getType());
        $this->assertFalse($columns['point_geography_2d_srid']->getUnsigned());
        $this->assertTrue($columns['point_geography_2d_srid']->getNotnull());
        $this->assertNull($columns['point_geography_2d_srid']->getDefault());
        $this->assertIsArray($columns['point_geography_2d_srid']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_geography_2d_srid']->getPlatformOption('geometry_type'));
        $this->assertEquals(4326, $columns['point_geography_2d_srid']->getPlatformOption('srid'));
    }

    public function testDiffListTableColumns(): void
    {
        $offlineTable = $this->createTableSchema();
        $onlineTable = $this->getSchemaManager()->introspectTable('points');

        $comparator = new Comparator(new PostGISPlatform());
        $diff = $comparator->compareTables($offlineTable, $onlineTable);

        $this->assertEmpty($diff->getAddedColumns(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getAddedForeignKeys(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getAddedIndexes(), 'No differences should be detected with the offline vs online schema.');

        $this->assertEmpty($diff->getRenamedColumns(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getRenamedIndexes(), 'No differences should be detected with the offline vs online schema.');

        $this->assertEmpty($diff->getModifiedColumns(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getModifiedForeignKeys(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getModifiedIndexes(), 'No differences should be detected with the offline vs online schema.');

        $this->assertEmpty($diff->getDroppedColumns(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getDroppedForeignKeys(), 'No differences should be detected with the offline vs online schema.');
        $this->assertEmpty($diff->getDroppedIndexes(), 'No differences should be detected with the offline vs online schema.');
    }

    public function testListTableIndexes(): void
    {
        $indexes = $this->getSchemaManager()->listTableIndexes('points');

        $spatialIndexes = [
            'idx_27ba8e293be136c3',
            'idx_27ba8e295f51a43c',
            'idx_27ba8e295afbb72d',
            'idx_27ba8e29b7a5f324',
            'idx_27ba8e293c257075',
            'idx_27ba8e2999674a3d',
            'idx_27ba8e29cf3dedbb',
            'idx_27ba8e29cf3dedbb',
            'idx_27ba8e293d5fe69e',
            'idx_27ba8e29b832b304',
        ];

        $nonSpatialIndexes = [
            'idx_text',
            'idx_text_gist',
        ];

        foreach ($spatialIndexes as $spatialIndex) {
            $this->assertArrayHasKey($spatialIndex, $indexes);
            $this->assertTrue($indexes[$spatialIndex]->hasFlag('spatial'));
        }

        foreach ($nonSpatialIndexes as $nonSpatialIndex) {
            $this->assertArrayHasKey($nonSpatialIndex, $indexes);
            $this->assertFalse($indexes[$nonSpatialIndex]->hasFlag('spatial'));
        }
    }

    private function createTableSchema(): Table
    {
        $table = new Table('points');
        $table->addColumn('id', 'integer', ['notnull' => true]);
        $table->addColumn('text', 'text', ['notnull' => true]);
        $table->addColumn('tsvector', 'tsvector', ['notnull' => true]);

        $table->addColumn('geometry', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'GEOMETRY',
                'srid' => 0,
            ]);

        $table->addColumn('point', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINT',
                'srid' => 0,
            ])
            ->setComment('This is a comment for column point');

        $table->addColumn('point_2d', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINT',
                'srid' => 3785,
            ]);

        $table->addColumn('point_3dz', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINTZ',
                'srid' => 3785,
            ]);

        $table->addColumn('point_3dm', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINTM',
                'srid' => 3785,
            ]);

        $table->addColumn('point_4d', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINTZM',
                'srid' => 3785,
            ]);

        $table->addColumn('point_2d_nullable', 'geometry', ['notnull' => false])
            ->setPlatformOptions([
                'geometry_type' => 'POINT',
                'srid' => 3785,
            ]);

        $table->addColumn('point_2d_nosrid', 'geometry', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINT',
                'srid' => 0,
            ]);

        $table->addColumn('geography', 'geography', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'GEOMETRY',
                'srid' => 4326,
            ]);

        $table->addColumn('point_geography_2d', 'geography', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINT',
                'srid' => 4326,
            ]);

        $table->addColumn('point_geography_2d_srid', 'geography', ['notnull' => true])
            ->setPlatformOptions([
                'geometry_type' => 'POINT',
                'srid' => 4326,
            ]);

        $table->addIndex(['text'], 'idx_text');
        $table->addIndex(['tsvector'], 'idx_text_gist');

        $table->addIndex(['point'], null, ['spatial']);
        $table->addIndex(['point_2d'], null, ['spatial']);
        $table->addIndex(['point_3dz'], null, ['spatial']);
        $table->addIndex(['point_3dm'], null, ['spatial']);
        $table->addIndex(['point_4d'], null, ['spatial']);
        $table->addIndex(['point_2d_nullable'], null, ['spatial']);
        $table->addIndex(['point_2d_nosrid'], null, ['spatial']);
        $table->addIndex(['point_geography_2d'], null, ['spatial']);
        $table->addIndex(['point_geography_2d_srid'], null, ['spatial']);

        $table->setComment('This is a comment for table points');

        $table->setPrimaryKey(['id']);

        return $table;
    }

    public function testAlterTableScenario(): void
    {
        $schemaManager = $this->getSchemaManager();
        $comparator = $schemaManager->createComparator();

        $fromTable = $schemaManager->introspectTable('points');
        $toTable = clone $fromTable;
        $toTable->addColumn('linestring', 'geometry', ['platformOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $toTable->dropColumn('point');
        $toTable->modifyColumn('point_3dm', ['platformOptions' => ['geometry_type' => 'pointm', 'srid' => 4326]]);
        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $schemaManager->alterTable($tableDiff);

        $table = $schemaManager->introspectTable('points');
        $this->assertFalse($table->hasColumn('point'));
        $this->assertTrue($table->hasColumn('linestring'));
        $this->assertEquals(4326, $table->getColumn('point_3dm')->getPlatformOption('srid'));

        $fromTable = $schemaManager->introspectTable('points');
        $toTable = clone $fromTable;
        $toTable->addIndex(['linestring'], 'linestring_idx', ['spatial']);
        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $schemaManager->alterTable($tableDiff);

        $table = $schemaManager->introspectTable('points');
        $this->assertTrue($table->hasIndex('linestring_idx'));
        $this->assertEquals(['linestring'], array_map('strtolower', $table->getIndex('linestring_idx')->getColumns()));
        $this->assertTrue($table->getIndex('linestring_idx')->hasFlag('spatial'));
        $this->assertFalse($table->getIndex('linestring_idx')->isPrimary());
        $this->assertFalse($table->getIndex('linestring_idx')->isUnique());

        $fromTable = $schemaManager->introspectTable('points');
        $indexes = $fromTable->getIndexes();
        $indexes['linestring_idx'] = new Index('linestring_idx', ['linestring', 'point_2d'], false, false, ['spatial']);
        $toTable = new Table(
            $fromTable->getName(),
            $fromTable->getColumns(),
            $indexes,
            $fromTable->getUniqueConstraints(),
            $fromTable->getForeignKeys(),
            $fromTable->getOptions(),
        );
        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $schemaManager->alterTable($tableDiff);

        $table = $schemaManager->introspectTable('points');
        $this->assertTrue($table->hasIndex('linestring_idx'));
        $this->assertEquals(['linestring', 'point_2d'], array_map('strtolower', $table->getIndex('linestring_idx')->getColumns()));

        $fromTable = $schemaManager->introspectTable('points');
        $indexes = $fromTable->getIndexes();
        unset($indexes['linestring_idx']);
        $indexes['linestring_renamed_idx'] = new Index('linestring_renamed_idx', ['linestring', 'point_2d'], false, false, ['spatial']);
        $toTable = new Table(
            $fromTable->getName(),
            $fromTable->getColumns(),
            $indexes,
            $fromTable->getUniqueConstraints(),
            $fromTable->getForeignKeys(),
            $fromTable->getOptions(),
        );
        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $schemaManager->alterTable($tableDiff);

        $table = $schemaManager->introspectTable('points');
        $this->assertTrue($table->hasIndex('linestring_renamed_idx'));
        $this->assertFalse($table->hasIndex('linestring_idx'));
        $this->assertEquals(['linestring', 'point_2d'], array_map('strtolower', $table->getIndex('linestring_renamed_idx')->getColumns()));
        $this->assertFalse($table->getIndex('linestring_renamed_idx')->isPrimary());
        $this->assertFalse($table->getIndex('linestring_renamed_idx')->isUnique());
    }

    private function getSchemaManager(): SchemaManager
    {
        return new SchemaManager($this->_getConnection(), $this->_getConnection()->getDatabasePlatform());
    }
}
