<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Driver;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\NumberType;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Schema\SchemaManager;
use Jsor\Doctrine\PostGIS\Types\GeoJsonType;

/**
 * @covers \Jsor\Doctrine\PostGIS\Driver\PostGISPlatform
 *
 * @internal
 */
final class PostGISPlatformTest extends AbstractFunctionalTestCase
{
    protected ?AbstractSchemaManager $sm;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_execFile('points_drop.sql');
        $this->_execFile('points_create.sql');

        $this->sm = $this->_getConnection()->createSchemaManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->_execFile('points_drop.sql');
    }

    public function testCreateSchemaManager(): void
    {
        $platform = new PostGISPlatform();
        $schemaManager = $platform->createSchemaManager($this->_getConnection());

        static::assertInstanceOf(SchemaManager::class, $schemaManager);
    }

    public function providerAlterSql(): iterable
    {
        static::_registerTypes();

        $comparator = new Comparator(new PostGISPlatform());

        $fromTable = new Table('points');
        $fromTable->addColumn('id', 'integer');
        $fromTable->addColumn('text', 'text');
        $fromTable->addColumn('point', 'geometry', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 3785]]);

        $toTable = clone $fromTable;
        $toTable->addIndex(['point'], 'point_idx', ['spatial']);

        $diff = $comparator->compareSchemas(new Schema([$fromTable]), new Schema([$toTable]));

        yield 'Create index' => [$diff, ['CREATE INDEX point_idx ON points USING gist(point)'], []];

        $fromTable = new Table('points');
        $fromTable->addColumn('point', 'geometry', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 3785]]);

        $toTable = new Table('points');
        $toTable->addColumn('point', 'geometry', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 4326]]);

        $diff = $comparator->compareSchemas(new Schema([$fromTable]), new Schema([$toTable]));

        yield 'Modify SRID' => [$diff, ["SELECT UpdateGeometrySRID('points', 'point', 4326)"], []];
    }

    /** @dataProvider providerAlterSql */
    public function testGetAlterSchemaSql(SchemaDiff $schemaDiff, array $expected, array $unexpected): void
    {
        $sql = (new PostGISPlatform())->getAlterSchemaSQL($schemaDiff);

        static::assertIndexes($expected, $unexpected, $sql);
    }

    /** @dataProvider providerAlterSql */
    public function testGetAlterTableSQL(SchemaDiff $schemaDiff, array $expected, array $unexpected): void
    {
        $tableDiffs = array_values($schemaDiff->getAlteredTables());
        $sql = (new PostGISPlatform())->getAlterTableSQL($tableDiffs[0]);

        static::assertIndexes($expected, $unexpected, $sql);
    }

    public function testGetAlterTableSQLCustomType(): void
    {
        if (!Type::hasType('geojson')) {
            Type::addType('geojson', GeoJsonType::class);
        }

        $comparator = new Comparator(new PostGISPlatform());

        $fromTable = new Table('points');
        $fromTable->addColumn('id', 'integer');
        $fromTable->addColumn('point', 'geometry', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 3785]]);

        $toTable = new Table('points');
        $toTable->addColumn('id', 'integer');
        $toTable->addColumn('point', 'geojson', ['platformOptions' => ['geometry_type' => 'point', 'srid' => 3785]]);
        $toTable->addColumn('boundary', 'geojson', ['platformOptions' => ['geometry_type' => 'multipolygon', 'srid' => 3785]]);

        $tableDiff = $comparator->compareTables($fromTable, $toTable);

        $sql = (new PostGISPlatform())->getAlterTableSQL($tableDiff);

        static::assertContains('ALTER TABLE points ADD boundary geojson(MULTIPOLYGON, 3785) NOT NULL', $sql);
        static::assertContains('ALTER TABLE points ALTER point TYPE geojson(POINT, 3785)', $sql);
    }

    public function testGetCreateTableSql(): void
    {
        $table = $this->sm->introspectTable('points');

        $sql = (new PostGISPlatform())->getCreateTableSQL($table);

        static::assertCreateTableSql($sql);
    }

    public function testGetCreateTablesSql(): void
    {
        $table1 = $this->sm->introspectTable('points');
        $table2 = new Table('places');
        $table2->addColumn('id', 'integer');
        $table2->addColumn('points_id', 'integer');
        $table2->setPrimaryKey(['id']);
        $table2->addForeignKeyConstraint($table1->getName(), ['points_id'], ['id']);

        $sql = (new PostGISPlatform())->getCreateTablesSQL([$table1, $table2]);

        static::assertCreateTableSql($sql);
        if (class_exists(NumberType::class)) {
            static::assertContains('ALTER TABLE places ADD CONSTRAINT FK_FEAF6C55DF69572F FOREIGN KEY (points_id) REFERENCES points (id)', $sql);
        } else {
            static::assertContains('ALTER TABLE places ADD CONSTRAINT FK_FEAF6C55DF69572F FOREIGN KEY (points_id) REFERENCES points (id) NOT DEFERRABLE INITIALLY IMMEDIATE', $sql);
        }
    }

    public function testGetCreateTableSqlSkipsAlreadyAddedTable(): void
    {
        $schema = new Schema([], [], $this->sm->createSchemaConfig());

        $this->_getMessengerConnection()->configureSchema($schema, $this->_getConnection(), fn () => true);

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
        $table = $this->sm->introspectTable('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getDropTableSQL($table->getName());

        $this->assertEquals('DROP TABLE points', $sql);
    }

    private static function assertCreateTableSql(array $sql): void
    {
        if (class_exists(NumberType::class)) {
            $expected = 'CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TEXT NOT NULL, geometry geometry(GEOMETRY, 0) NOT NULL, point geometry(POINT, 0) NOT NULL, point_2d geometry(POINT, 3785) NOT NULL, point_3dz geometry(POINTZ, 3785) NOT NULL, point_3dm geometry(POINTM, 3785) NOT NULL, point_4d geometry(POINTZM, 3785) NOT NULL, point_2d_nullable geometry(POINT, 3785) DEFAULT NULL, point_2d_nosrid geometry(POINT, 0) NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT, 4326) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY (id))';
        } else {
            $expected = 'CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TEXT NOT NULL, geometry geometry(GEOMETRY, 0) NOT NULL, point geometry(POINT, 0) NOT NULL, point_2d geometry(POINT, 3785) NOT NULL, point_3dz geometry(POINTZ, 3785) NOT NULL, point_3dm geometry(POINTM, 3785) NOT NULL, point_4d geometry(POINTZM, 3785) NOT NULL, point_2d_nullable geometry(POINT, 3785) DEFAULT NULL, point_2d_nosrid geometry(POINT, 0) NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT, 4326) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY(id))';
        }
        static::assertContains($expected, $sql);

        static::assertContains("COMMENT ON TABLE points IS 'This is a comment for table points'", $sql);
        static::assertContains("COMMENT ON COLUMN points.point IS 'This is a comment for column point'", $sql);

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
            static::assertContains($spatialIndex, $sql);
        }
    }

    private static function assertIndexes(array $expected, array $unexpected, array $sql): void
    {
        foreach ($expected as $spatialIndex) {
            static::assertContains($spatialIndex, $sql);
        }
        foreach ($unexpected as $spatialIndex) {
            static::assertNotContains($spatialIndex, $sql);
        }
    }
}
