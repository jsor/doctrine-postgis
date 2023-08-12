<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use RuntimeException;

final class DBALSchemaEventSubscriberTest extends AbstractFunctionalTestCase
{
    protected ?AbstractSchemaManager $sm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_execFile('points_drop.sql');
        $this->_execFile('points_create.sql');

        $this->sm = $this->_getConnection()->getSchemaManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->_execFile('points_drop.sql');
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

    public function testListTableColumns(): void
    {
        $columns = $this->sm->listTableColumns('points');

        $this->assertArrayHasKey('point', $columns);
        $this->assertEquals('point', strtolower($columns['point']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point']->getType());
        $this->assertFalse($columns['point']->getUnsigned());
        $this->assertTrue($columns['point']->getNotnull());
        $this->assertNull($columns['point']->getDefault());
        $this->assertIsArray($columns['point']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point']->getPlatformOption('geometry_type'));
        $this->assertEquals(0, $columns['point']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d', $columns);
        $this->assertEquals('point_2d', strtolower($columns['point_2d']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_2d']->getType());
        $this->assertFalse($columns['point_2d']->getUnsigned());
        $this->assertTrue($columns['point_2d']->getNotnull());
        $this->assertNull($columns['point_2d']->getDefault());
        $this->assertIsArray($columns['point_2d']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_2d']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_3dz', $columns);
        $this->assertEquals('point_3dz', strtolower($columns['point_3dz']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_3dz']->getType());
        $this->assertFalse($columns['point_3dz']->getUnsigned());
        $this->assertTrue($columns['point_3dz']->getNotnull());
        $this->assertNull($columns['point_3dz']->getDefault());
        $this->assertIsArray($columns['point_3dz']->getPlatformOptions());

        $this->assertEquals('POINTZ', $columns['point_3dz']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_3dz']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_3dm', $columns);
        $this->assertEquals('point_3dm', strtolower($columns['point_3dm']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_3dm']->getType());
        $this->assertFalse($columns['point_3dm']->getUnsigned());
        $this->assertTrue($columns['point_3dm']->getNotnull());
        $this->assertNull($columns['point_3dm']->getDefault());
        $this->assertIsArray($columns['point_3dm']->getPlatformOptions());

        $this->assertEquals('POINTM', $columns['point_3dm']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_3dm']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d_nosrid', $columns);
        $this->assertEquals('point_4d', strtolower($columns['point_4d']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_4d']->getType());
        $this->assertFalse($columns['point_4d']->getUnsigned());
        $this->assertTrue($columns['point_4d']->getNotnull());
        $this->assertNull($columns['point_4d']->getDefault());
        $this->assertIsArray($columns['point_4d']->getPlatformOptions());

        $this->assertEquals('POINTZM', $columns['point_4d']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_4d']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d_nullable', $columns);
        $this->assertEquals('point_2d_nullable', strtolower($columns['point_2d_nullable']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_2d_nullable']->getType());
        $this->assertFalse($columns['point_2d_nullable']->getUnsigned());
        $this->assertFalse($columns['point_2d_nullable']->getNotnull());
        // $this->assertEquals('NULL::geometry', $columns['point_2d_nullable']->getDefault());
        $this->assertIsArray($columns['point_2d_nullable']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d_nullable']->getPlatformOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_2d_nullable']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d_nosrid', $columns);
        $this->assertEquals('point_2d_nosrid', strtolower($columns['point_2d_nosrid']->getName()));
        $this->assertInstanceOf(GeometryType::class, $columns['point_2d_nosrid']->getType());
        $this->assertFalse($columns['point_2d_nosrid']->getUnsigned());
        $this->assertTrue($columns['point_2d_nosrid']->getNotnull());
        $this->assertNull($columns['point_2d_nosrid']->getDefault());
        $this->assertIsArray($columns['point_2d_nosrid']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d_nosrid']->getPlatformOption('geometry_type'));
        $this->assertEquals(0, $columns['point_2d_nosrid']->getPlatformOption('srid'));

        // ---

        $this->assertArrayHasKey('point_geography_2d', $columns);
        $this->assertEquals('point_geography_2d', strtolower($columns['point_geography_2d']->getName()));
        $this->assertInstanceOf(GeographyType::class, $columns['point_geography_2d']->getType());
        $this->assertFalse($columns['point_geography_2d']->getUnsigned());
        $this->assertTrue($columns['point_geography_2d']->getNotnull());
        $this->assertNull($columns['point_geography_2d']->getDefault());
        $this->assertIsArray($columns['point_geography_2d']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_geography_2d']->getPlatformOption('geometry_type'));
        $this->assertEquals(4326, $columns['point_geography_2d']->getPlatformOption('srid'));

        // ---

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
        $onlineTable = $this->sm->listTableDetails('points');

        $comparator = new Comparator();
        $diff = $comparator->diffTable($offlineTable, $onlineTable);

        $this->assertFalse($diff, 'No differences should be detected with the offline vs online schema.');
    }

    public function testListTableIndexes(): void
    {
        $indexes = $this->sm->listTableIndexes('points');

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

    public function testGetCreateTableSql(): void
    {
        $table = $this->sm->listTableDetails('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getCreateTableSQL($table);

        $expected = 'CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TEXT NOT NULL, geometry geometry(GEOMETRY, 0) NOT NULL, point geometry(POINT, 0) NOT NULL, point_2d geometry(POINT, 3785) NOT NULL, point_3dz geometry(POINTZ, 3785) NOT NULL, point_3dm geometry(POINTM, 3785) NOT NULL, point_4d geometry(POINTZM, 3785) NOT NULL, point_2d_nullable geometry(POINT, 3785) DEFAULT NULL, point_2d_nosrid geometry(POINT, 0) NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT, 4326) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY(id))';
        $this->assertContains($expected, $sql);

        $this->assertContains("COMMENT ON TABLE points IS 'This is a comment for table points'", $sql);
        $this->assertContains("COMMENT ON COLUMN points.point IS 'This is a comment for column point'", $sql);

        $spatialIndexes = [
            'CREATE INDEX idx_27ba8e29b7a5f324 ON points USING gist(point)',
            'CREATE INDEX idx_27ba8e2999674a3d ON points USING gist(point_2d)',
            'CREATE INDEX idx_27ba8e293be136c3 ON points USING gist(point_3dz)',
            'CREATE INDEX idx_27ba8e29b832b304 ON points USING gist(point_3dm)',
            'CREATE INDEX idx_27ba8e29cf3dedbb ON points USING gist(point_4d)',
            'CREATE INDEX idx_27ba8e293c257075 ON points USING gist(point_2d_nullable)',
            'CREATE INDEX idx_27ba8e293d5fe69e ON points USING gist(point_2d_nosrid)',
            'CREATE INDEX idx_27ba8e295f51a43c ON points USING gist(point_geography_2d)',
            'CREATE INDEX idx_27ba8e295afbb72d ON points USING gist(point_geography_2d_srid)',
        ];

        foreach ($spatialIndexes as $spatialIndex) {
            $this->assertContains($spatialIndex, $sql);
        }
    }

    public function testGetCreateTableSqlSkipsAlreadyAddedTable(): void
    {
        $schema = new Schema([], [], $this->sm->createSchemaConfig());

        $this->_getMessengerConnection()->configureSchema($schema, $this->_getConnection(), static fn () => false);

        $sql = $this->_getConnection()->getDatabasePlatform()->getCreateTableSQL($schema->getTable('messenger_messages'));

        $expected = $sql[0];
        $this->assertStringStartsWith('CREATE TABLE messenger_messages', $expected);

        unset($sql[0]);

        // Assert that the CREATE TABLE statement for the messenger_messages
        // table exists only once
        $this->assertNotContains($expected, $sql);
    }

    public function testGetDropTableSql(): void
    {
        $table = $this->sm->listTableDetails('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getDropTableSQL($table);

        $this->assertEquals('DROP TABLE points', $sql);
    }

    public function testAlterTableScenario(): void
    {
        $table = $this->sm->listTableDetails('points');

        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->addedColumns['linestring'] = new Column('linestring', Type::getType('geometry'), ['customSchemaOptions' => ['geometry_type' => 'linestring', 'srid' => 3785]]);
        $tableDiff->removedColumns['point'] = $table->getColumn('point');
        $tableDiff->changedColumns[] = new ColumnDiff('point_3dm', new Column('point_3dm', Type::getType('geometry'), ['customSchemaOptions' => ['srid' => 4326]]), ['srid'], $table->getColumn('point_3dm'));

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertFalse($table->hasColumn('point'));
        $this->assertTrue($table->hasColumn('linestring'));
        $this->assertEquals(4326, $table->getColumn('point_3dm')->getPlatformOption('srid'));

        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->addedIndexes[] = new Index('linestring_idx', ['linestring'], false, false, ['spatial']);

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertTrue($table->hasIndex('linestring_idx'));
        $this->assertEquals(['linestring'], array_map('strtolower', $table->getIndex('linestring_idx')->getColumns()));
        $this->assertTrue($table->getIndex('linestring_idx')->hasFlag('spatial'));
        $this->assertFalse($table->getIndex('linestring_idx')->isPrimary());
        $this->assertFalse($table->getIndex('linestring_idx')->isUnique());

        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedIndexes[] = new Index('linestring_idx', ['linestring', 'point_2d'], false, false, ['spatial']);

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertTrue($table->hasIndex('linestring_idx'));
        $this->assertEquals(['linestring', 'point_2d'], array_map('strtolower', $table->getIndex('linestring_idx')->getColumns()));

        $tableDiff = new TableDiff('points');

        $tableDiff->fromTable = $table;
        $tableDiff->renamedIndexes['linestring_idx'] = new Index('linestring_renamed_idx', ['linestring', 'point_2d'], false, false, ['spatial']);

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertTrue($table->hasIndex('linestring_renamed_idx'));
        $this->assertFalse($table->hasIndex('linestring_idx'));
        $this->assertEquals(['linestring', 'point_2d'], array_map('strtolower', $table->getIndex('linestring_renamed_idx')->getColumns()));
        $this->assertFalse($table->getIndex('linestring_renamed_idx')->isPrimary());
        $this->assertFalse($table->getIndex('linestring_renamed_idx')->isUnique());
    }

    public function testAlterTableThrowsExceptionForChangedType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The type of a spatial column cannot be changed (Requested changing type from "geometry" to "geography" for column "point_2d" in table "points")');

        $table = $this->sm->listTableDetails('points');

        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedColumns[] = new ColumnDiff('point_2d', new Column('point_2d', Type::getType('geography'), []), ['type'], $table->getColumn('point_2d'));

        $this->sm->alterTable($tableDiff);
    }

    public function testAlterTableThrowsExceptionForChangedSpatialType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The geometry_type of a spatial column cannot be changed (Requested changing type from "POINT" to "LINESTRING" for column "point_2d" in table "points")');

        $table = $this->sm->listTableDetails('points');

        $tableDiff = new TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedColumns[] = new ColumnDiff('point_2d', new Column('point_2d', Type::getType('geometry'), ['customSchemaOptions' => ['geometry_type' => 'LINESTRING']]), ['geometry_type'], $table->getColumn('point_2d'));

        $this->sm->alterTable($tableDiff);
    }
}
