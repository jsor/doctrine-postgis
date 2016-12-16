<?php

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;

class DBALSchemaEventSubscriberTest extends AbstractFunctionalTestCase
{
    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    protected $sm;

    protected function setUp()
    {
        parent::setUp();

        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_points_drop.sql');
        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_points_create.sql');

        $this->sm = $this->_getConnection()->getSchemaManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_points_drop.sql');
    }

    protected function createTableSchema()
    {
        $table = new \Doctrine\DBAL\Schema\Table('points');
        $table->addColumn('id', 'integer', array('notnull' => true));
        $table->addColumn('text', 'text', array('notnull' => true));
        $table->addColumn('tsvector', 'tsvector', array('notnull' => true));

        $table->addColumn('geometry', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'GEOMETRY',
                'srid' => 0,
            ));

        $table->addColumn('point', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINT',
                'srid' => 0,
            ));

        $table->addColumn('point_2d', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINT',
                'srid' => 3785,
            ));

        $table->addColumn('point_3dz', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINTZ',
                'srid' => 3785,
            ));

        $table->addColumn('point_3dm', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINTM',
                'srid' => 3785,
            ));

        $table->addColumn('point_4d', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINTZM',
                'srid' => 3785,
            ));

        $table->addColumn('point_2d_nullable', 'geometry', array('notnull' => false))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINT',
                'srid' => 3785,
            ));

        $table->addColumn('point_2d_nosrid', 'geometry', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINT',
                'srid' => 0,
            ));

        $table->addColumn('geography', 'geography', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'GEOMETRY',
                'srid' => 4326,
            ));

        $table->addColumn('point_geography_2d', 'geography', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINT',
                'srid' => 4326,
            ));

        $table->addColumn('point_geography_2d_srid', 'geography', array('notnull' => true))
            ->setCustomSchemaOptions(array(
                'geometry_type' => 'POINT',
                'srid' => 4326,
            ));

        $table->addIndex(array('text'), 'idx_text');
        $table->addIndex(array('tsvector'), 'idx_text_gist');

        $table->addIndex(array('point'), null, array('spatial'));
        $table->addIndex(array('point_2d'), null, array('spatial'));
        $table->addIndex(array('point_3dz'), null, array('spatial'));
        $table->addIndex(array('point_3dm'), null, array('spatial'));
        $table->addIndex(array('point_4d'), null, array('spatial'));
        $table->addIndex(array('point_2d_nullable'), null, array('spatial'));
        $table->addIndex(array('point_2d_nosrid'), null, array('spatial'));
        $table->addIndex(array('point_geography_2d'), null, array('spatial'));
        $table->addIndex(array('point_geography_2d_srid'), null, array('spatial'));

        $table->setPrimaryKey(array('id'));

        return $table;
    }

    public function testSubscriberThrowsWhenRegisteredOnMultipleConnections()
    {
        $this->setExpectedException(
            '\LogicException',
            'It looks like you have registered the Jsor\Doctrine\PostGIS\Event\DBALSchemaEventSubscriber to more than one connection. Please register one instance per connection.'
        );

        $subscriber = new DBALSchemaEventSubscriber();

        $conn1 = DriverManager::getConnection($this->_getDbParams());
        $conn1->getEventManager()->addEventSubscriber($subscriber);

        $conn2 = DriverManager::getConnection($this->_getDbParams());
        $conn2->getEventManager()->addEventSubscriber($subscriber);

        $conn1->connect();
        $conn2->connect();
    }

    public function testSubscriberDoesNotThrowWhenRegisteredOnMasterSlaveConnectionAndConnectionSwitches()
    {
        $subscriber = new DBALSchemaEventSubscriber();

        $dbParams = $this->_getDbParams();

        $conn = DriverManager::getConnection(array(
            'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
            'driver' => $dbParams['driver'],
            'master' => $dbParams,
            'slaves' => array(
                $dbParams
            )
        ));

        $conn->getEventManager()->addEventSubscriber($subscriber);

        $conn->connect('slave');
        $conn->connect('master');
    }

    public function testListTableColumns()
    {
        $columns = $this->sm->listTableColumns('points');

        $this->assertArrayHasKey('point', $columns);
        $this->assertEquals('point', strtolower($columns['point']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point']->getType());
        $this->assertEquals(false, $columns['point']->getUnsigned());
        $this->assertEquals(true, $columns['point']->getNotnull());
        $this->assertEquals(null, $columns['point']->getDefault());
        $this->assertInternalType('array', $columns['point']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(0, $columns['point']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d', $columns);
        $this->assertEquals('point_2d', strtolower($columns['point_2d']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point_2d']->getType());
        $this->assertEquals(false, $columns['point_2d']->getUnsigned());
        $this->assertEquals(true, $columns['point_2d']->getNotnull());
        $this->assertEquals(null, $columns['point_2d']->getDefault());
        $this->assertInternalType('array', $columns['point_2d']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_2d']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_3dz', $columns);
        $this->assertEquals('point_3dz', strtolower($columns['point_3dz']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point_3dz']->getType());
        $this->assertEquals(false, $columns['point_3dz']->getUnsigned());
        $this->assertEquals(true, $columns['point_3dz']->getNotnull());
        $this->assertEquals(null, $columns['point_3dz']->getDefault());
        $this->assertInternalType('array', $columns['point_3dz']->getPlatformOptions());

        $this->assertEquals('POINTZ', $columns['point_3dz']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_3dz']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_3dm', $columns);
        $this->assertEquals('point_3dm', strtolower($columns['point_3dm']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point_3dm']->getType());
        $this->assertEquals(false, $columns['point_3dm']->getUnsigned());
        $this->assertEquals(true, $columns['point_3dm']->getNotnull());
        $this->assertEquals(null, $columns['point_3dm']->getDefault());
        $this->assertInternalType('array', $columns['point_3dm']->getPlatformOptions());

        $this->assertEquals('POINTM', $columns['point_3dm']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_3dm']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d_nosrid', $columns);
        $this->assertEquals('point_4d', strtolower($columns['point_4d']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point_4d']->getType());
        $this->assertEquals(false, $columns['point_4d']->getUnsigned());
        $this->assertEquals(true, $columns['point_4d']->getNotnull());
        $this->assertEquals(null, $columns['point_4d']->getDefault());
        $this->assertInternalType('array', $columns['point_4d']->getPlatformOptions());

        $this->assertEquals('POINTZM', $columns['point_4d']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_4d']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d_nullable', $columns);
        $this->assertEquals('point_2d_nullable', strtolower($columns['point_2d_nullable']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point_2d_nullable']->getType());
        $this->assertEquals(false, $columns['point_2d_nullable']->getUnsigned());
        $this->assertEquals(false, $columns['point_2d_nullable']->getNotnull());
        //$this->assertEquals('NULL::geometry', $columns['point_2d_nullable']->getDefault());
        $this->assertInternalType('array', $columns['point_2d_nullable']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d_nullable']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(3785, $columns['point_2d_nullable']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_2d_nosrid', $columns);
        $this->assertEquals('point_2d_nosrid', strtolower($columns['point_2d_nosrid']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeometryType', $columns['point_2d_nosrid']->getType());
        $this->assertEquals(false, $columns['point_2d_nosrid']->getUnsigned());
        $this->assertEquals(true, $columns['point_2d_nosrid']->getNotnull());
        $this->assertEquals(null, $columns['point_2d_nosrid']->getDefault());
        $this->assertInternalType('array', $columns['point_2d_nosrid']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_2d_nosrid']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(0, $columns['point_2d_nosrid']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_geography_2d', $columns);
        $this->assertEquals('point_geography_2d', strtolower($columns['point_geography_2d']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeographyType', $columns['point_geography_2d']->getType());
        $this->assertEquals(false, $columns['point_geography_2d']->getUnsigned());
        $this->assertEquals(true, $columns['point_geography_2d']->getNotnull());
        $this->assertEquals(null, $columns['point_geography_2d']->getDefault());
        $this->assertInternalType('array', $columns['point_geography_2d']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_geography_2d']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(4326, $columns['point_geography_2d']->getCustomSchemaOption('srid'));

        // ---

        $this->assertArrayHasKey('point_geography_2d_srid', $columns);
        $this->assertEquals('point_geography_2d_srid', strtolower($columns['point_geography_2d_srid']->getName()));
        $this->assertInstanceOf('Jsor\Doctrine\PostGIS\Types\GeographyType', $columns['point_geography_2d_srid']->getType());
        $this->assertEquals(false, $columns['point_geography_2d_srid']->getUnsigned());
        $this->assertEquals(true, $columns['point_geography_2d_srid']->getNotnull());
        $this->assertEquals(null, $columns['point_geography_2d_srid']->getDefault());
        $this->assertInternalType('array', $columns['point_geography_2d_srid']->getPlatformOptions());

        $this->assertEquals('POINT', $columns['point_geography_2d_srid']->getCustomSchemaOption('geometry_type'));
        $this->assertEquals(4326, $columns['point_geography_2d_srid']->getCustomSchemaOption('srid'));
    }

    public function testDiffListTableColumns()
    {
        $offlineTable = $this->createTableSchema();
        $onlineTable = $this->sm->listTableDetails('points');

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $diff = $comparator->diffTable($offlineTable, $onlineTable);

        $this->assertFalse($diff, 'No differences should be detected with the offline vs online schema.');
    }

    public function testListTableIndexes()
    {
        $indexes = $this->sm->listTableIndexes('points');

        $spatialIndexes = array(
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
        );

        $nonSpatialIndexes = array(
            'idx_text',
            'idx_text_gist',
        );

        foreach ($spatialIndexes as $spatialIndex) {
            $this->assertArrayHasKey($spatialIndex, $indexes);
            $this->assertTrue($indexes[$spatialIndex]->hasFlag('spatial'));
        }

        foreach ($nonSpatialIndexes as $nonSpatialIndex) {
            $this->assertArrayHasKey($nonSpatialIndex, $indexes);
            $this->assertFalse($indexes[$nonSpatialIndex]->hasFlag('spatial'));
        }
    }

    /**
     * @group postgis-2.x
     */
    public function testGetCreateTableSqlPostGIS2x()
    {
        $table = $this->sm->listTableDetails('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getCreateTableSQL($table);

        $expected = 'CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TEXT NOT NULL, geometry geometry(GEOMETRY, 0) NOT NULL, point geometry(POINT, 0) NOT NULL, point_2d geometry(POINT, 3785) NOT NULL, point_3dz geometry(POINTZ, 3785) NOT NULL, point_3dm geometry(POINTM, 3785) NOT NULL, point_4d geometry(POINTZM, 3785) NOT NULL, point_2d_nullable geometry(POINT, 3785) DEFAULT NULL, point_2d_nosrid geometry(POINT, 0) NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT, 4326) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY(id))';
        $this->assertContains($expected, $sql);

        $spatialIndexes = array(
            'CREATE INDEX idx_27ba8e29b7a5f324 ON points USING gist(point)',
            'CREATE INDEX idx_27ba8e2999674a3d ON points USING gist(point_2d)',
            'CREATE INDEX idx_27ba8e293be136c3 ON points USING gist(point_3dz)',
            'CREATE INDEX idx_27ba8e29b832b304 ON points USING gist(point_3dm)',
            'CREATE INDEX idx_27ba8e29cf3dedbb ON points USING gist(point_4d)',
            'CREATE INDEX idx_27ba8e293c257075 ON points USING gist(point_2d_nullable)',
            'CREATE INDEX idx_27ba8e293d5fe69e ON points USING gist(point_2d_nosrid)',
            'CREATE INDEX idx_27ba8e295f51a43c ON points USING gist(point_geography_2d)',
            'CREATE INDEX idx_27ba8e295afbb72d ON points USING gist(point_geography_2d_srid)',
        );

        foreach ($spatialIndexes as $spatialIndex) {
            $this->assertContains($spatialIndex, $sql);
        }
    }

    /**
     * @group postgis-1.5
     */
    public function testGetCreateTableSqlPostGIS15()
    {
        $table = $this->sm->listTableDetails('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getCreateTableSQL($table);

        $expected = 'CREATE TABLE points (id INT NOT NULL, text TEXT NOT NULL, tsvector TEXT NOT NULL, geography geography(GEOMETRY, 4326) NOT NULL, point_geography_2d geography(POINT, 4326) NOT NULL, point_geography_2d_srid geography(POINT, 4326) NOT NULL, PRIMARY KEY(id))';
        $this->assertContains($expected, $sql);

        $columns = array(
            "SELECT AddGeometryColumn('points', 'geometry', -1, 'GEOMETRY', 2)",
            'ALTER TABLE points ALTER point SET NOT NULL',
            "SELECT AddGeometryColumn('points', 'point', -1, 'POINT', 2)",
            'ALTER TABLE points ALTER point SET NOT NULL',
            "SELECT AddGeometryColumn('points', 'point_2d', 3785, 'POINT', 2)",
            'ALTER TABLE points ALTER point_2d SET NOT NULL',
            "SELECT AddGeometryColumn('points', 'point_3dz', 3785, 'POINT', 3)",
            'ALTER TABLE points ALTER point_3dz SET NOT NULL',
            "SELECT AddGeometryColumn('points', 'point_3dm', 3785, 'POINTM', 3)",
            'ALTER TABLE points ALTER point_3dm SET NOT NULL',
            "SELECT AddGeometryColumn('points', 'point_4d', 3785, 'POINT', 4)",
            'ALTER TABLE points ALTER point_4d SET NOT NULL',
            "SELECT AddGeometryColumn('points', 'point_2d_nullable', 3785, 'POINT', 2)",
            "SELECT AddGeometryColumn('points', 'point_2d_nosrid', -1, 'POINT', 2)",
            'ALTER TABLE points ALTER point_2d_nosrid SET NOT NULL',
        );

        foreach ($columns as $column) {
            $this->assertContains($column, $sql);
        }

        $spatialIndexes = array(
            'CREATE INDEX idx_27ba8e29b7a5f324 ON points USING gist(point)',
            'CREATE INDEX idx_27ba8e2999674a3d ON points USING gist(point_2d)',
            'CREATE INDEX idx_27ba8e293be136c3 ON points USING gist(point_3dz)',
            'CREATE INDEX idx_27ba8e29b832b304 ON points USING gist(point_3dm)',
            'CREATE INDEX idx_27ba8e29cf3dedbb ON points USING gist(point_4d)',
            'CREATE INDEX idx_27ba8e293c257075 ON points USING gist(point_2d_nullable)',
            'CREATE INDEX idx_27ba8e293d5fe69e ON points USING gist(point_2d_nosrid)',
            'CREATE INDEX idx_27ba8e295f51a43c ON points USING gist(point_geography_2d)',
            'CREATE INDEX idx_27ba8e295afbb72d ON points USING gist(point_geography_2d_srid)',
        );

        foreach ($spatialIndexes as $spatialIndex) {
            $this->assertContains($spatialIndex, $sql);
        }
    }

    /**
     * @group postgis-2.x
     */
    public function testGetDropTableSqlPostGIS2x()
    {
        $table = $this->sm->listTableDetails('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getDropTableSQL($table);

        $this->assertEquals('DROP TABLE points', $sql);
    }

    /**
     * @group postgis-1.5
     */
    public function testGetDropTableSqlPostGIS15()
    {
        $table = $this->sm->listTableDetails('points');

        $sql = $this->_getConnection()->getDatabasePlatform()->getDropTableSQL($table);

        $this->assertEquals("SELECT DropGeometryTable('points')", $sql);
    }

    public function testAlterTableScenario()
    {
        $table = $this->sm->listTableDetails('points');

        $tableDiff = new \Doctrine\DBAL\Schema\TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->addedColumns['linestring'] = new \Doctrine\DBAL\Schema\Column('linestring', Type::getType('geometry'), array('customSchemaOptions' => array('geometry_type' => 'linestring', 'srid' => 3785)));
        $tableDiff->removedColumns['point'] = $table->getColumn('point');
        $tableDiff->changedColumns[] = new ColumnDiff('point_3dm', new \Doctrine\DBAL\Schema\Column('point_3dm', Type::getType('geometry'), array('customSchemaOptions' => array('srid' => 4326))), array('srid'), $table->getColumn('point_3dm'));

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertFalse($table->hasColumn('point'));
        $this->assertTrue($table->hasColumn('linestring'));
        $this->assertEquals(4326, $table->getColumn('point_3dm')->getCustomSchemaOption('srid'));

        $tableDiff = new \Doctrine\DBAL\Schema\TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->addedIndexes[] = new \Doctrine\DBAL\Schema\Index('linestring_idx', array('linestring'), false, false, array('spatial'));

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertTrue($table->hasIndex('linestring_idx'));
        $this->assertEquals(array('linestring'), array_map('strtolower', $table->getIndex('linestring_idx')->getColumns()));
        $this->assertTrue($table->getIndex('linestring_idx')->hasFlag('spatial'));
        $this->assertFalse($table->getIndex('linestring_idx')->isPrimary());
        $this->assertFalse($table->getIndex('linestring_idx')->isUnique());

        $tableDiff = new \Doctrine\DBAL\Schema\TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedIndexes[] = new \Doctrine\DBAL\Schema\Index('linestring_idx', array('linestring', 'point_2d'), false, false, array('spatial'));

        $this->sm->alterTable($tableDiff);

        $table = $this->sm->listTableDetails('points');
        $this->assertTrue($table->hasIndex('linestring_idx'));
        $this->assertEquals(array('linestring', 'point_2d'), array_map('strtolower', $table->getIndex('linestring_idx')->getColumns()));

        $tableDiff = new \Doctrine\DBAL\Schema\TableDiff('points');

        // renamedIndexes added in 2.5
        if (isset($tableDiff->renamedIndexes)) {
            $tableDiff->fromTable = $table;
            $tableDiff->renamedIndexes['linestring_idx'] = new \Doctrine\DBAL\Schema\Index('linestring_renamed_idx', array('linestring', 'point_2d'), false, false, array('spatial'));

            $this->sm->alterTable($tableDiff);

            $table = $this->sm->listTableDetails('points');
            $this->assertTrue($table->hasIndex('linestring_renamed_idx'));
            $this->assertFalse($table->hasIndex('linestring_idx'));
            $this->assertEquals(array('linestring', 'point_2d'), array_map('strtolower', $table->getIndex('linestring_renamed_idx')->getColumns()));
            $this->assertFalse($table->getIndex('linestring_renamed_idx')->isPrimary());
            $this->assertFalse($table->getIndex('linestring_renamed_idx')->isUnique());
        }
    }

    public function testAlterTableThrowsExceptionForChangedType()
    {
        $this->setExpectedException('\RuntimeException', 'The type of a spatial column cannot be changed (Requested changing type from "geometry" to "geography" for column "point_2d" in table "points")');
        $table = $this->sm->listTableDetails('points');

        $tableDiff = new \Doctrine\DBAL\Schema\TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedColumns[] = new ColumnDiff('point_2d', new \Doctrine\DBAL\Schema\Column('point_2d', Type::getType('geography'), array()), array('type'), $table->getColumn('point_2d'));

        $this->sm->alterTable($tableDiff);
    }

    public function testAlterTableThrowsExceptionForChangedSpatialType()
    {
        $this->setExpectedException('\RuntimeException', 'The geometry_type of a spatial column cannot be changed (Requested changing type from "POINT" to "LINESTRING" for column "point_2d" in table "points")');
        $table = $this->sm->listTableDetails('points');

        $tableDiff = new \Doctrine\DBAL\Schema\TableDiff('points');
        $tableDiff->fromTable = $table;
        $tableDiff->changedColumns[] = new ColumnDiff('point_2d', new \Doctrine\DBAL\Schema\Column('point_2d', Type::getType('geometry'), array('customSchemaOptions' => array('geometry_type' => 'LINESTRING'))), array('geometry_type'), $table->getColumn('point_2d'));

        $this->sm->alterTable($tableDiff);
    }
}
