<?php

namespace Jsor\Doctrine\PostGIS\Schema;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;

class SchemaManagerTest extends AbstractFunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_points_drop.sql');
        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_points_create.sql');

        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_reserved-words_drop.sql');
        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_reserved-words_create.sql');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_points_drop.sql');

        $this->_execFile('postgis-' . getenv('POSTGIS_VERSION') . '_reserved-words_drop.sql');
    }

    public function testListSpatialIndexes()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = array(
            'idx_27ba8e29b7a5f324' => array(
                0 => 'point',
            ),
            'idx_27ba8e2999674a3d' => array(
                0 => 'point_2d',
            ),
            'idx_27ba8e293be136c3' => array(
                0 => 'point_3dz',
            ),
            'idx_27ba8e29b832b304' => array(
                0 => 'point_3dm',
            ),
            'idx_27ba8e29cf3dedbb' => array(
                0 => 'point_4d',
            ),
            'idx_27ba8e293c257075' => array(
                0 => 'point_2d_nullable',
            ),
            'idx_27ba8e293d5fe69e' => array(
                0 => 'point_2d_nosrid',
            ),
            'idx_27ba8e295f51a43c' => array(
                0 => 'point_geography_2d',
            ),
            'idx_27ba8e295afbb72d' => array(
                0 => 'point_geography_2d_srid',
            ),
        );

        $this->assertEquals($expected, $schemaManager->listSpatialIndexes('foo.points'));
    }

    public function testListSpatialGeometryColumns()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = array(
            'geometry',
            'point',
            'point_2d',
            'point_3dz',
            'point_3dm',
            'point_4d',
            'point_2d_nullable',
            'point_2d_nosrid',
        );

        $this->assertEquals($expected, $schemaManager->listSpatialGeometryColumns('foo.points'));
    }

    public function testListSpatialGeometryColumnsWithReservedWords()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = array(
            'user'
        );

        $this->assertEquals($expected, $schemaManager->listSpatialGeometryColumns('"user"'));
    }

    public function testGetGeometrySpatialColumnInfo()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $this->assertNull($schemaManager->getGeometrySpatialColumnInfo('foo.points', 'text'));

        $expected = array(
            'type' => 'GEOMETRY',
            'srid' => 0,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'geometry'));

        $expected = array(
            'type' => 'POINT',
            'srid' => 0,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point'));

        $expected = array(
            'type' => 'POINT',
            'srid' => 3785,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_2d'));

        $expected = array(
            'type' => 'POINTZ',
            'srid' => 3785,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_3dz'));

        $expected = array(
            'type' => 'POINTM',
            'srid' => 3785,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_3dm'));

        $expected = array(
            'type' => 'POINTZM',
            'srid' => 3785,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_4d'));

        $expected = array(
            'type' => 'POINT',
            'srid' => 3785,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_2d_nullable'));

        $expected = array(
            'type' => 'POINT',
            'srid' => 0,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('points', 'point_2d_nosrid'));
    }

    public function testGetGeographySpatialColumnInfo()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $this->assertNull($schemaManager->getGeographySpatialColumnInfo('foo.points', 'text'));

        $expected = array(
            'type' => 'GEOMETRY',
            'srid' => 4326,
        );
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('points', 'geography'));

        $expected = array(
            'type' => 'POINT',
            'srid' => 4326,
        );
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('points', 'point_geography_2d'));

        $expected = array(
            'type' => 'POINT',
            'srid' => 4326,
        );
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('points', 'point_geography_2d_srid'));
    }

    public function testGetGeometrySpatialColumnInfoWithReservedWords()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = array(
            'type' => 'GEOMETRY',
            'srid' => 0,
        );
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('"user"', '"user"'));
    }

    public function testGetGeographySpatialColumnInfoWithReservedWords()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = array(
            'type' => 'GEOMETRY',
            'srid' => 4326,
        );
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('"user"', '"primary"'));
    }

    /**
     * @group postgis-1.5
     */
    public function testIsPostGis2OnPostGIS15()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $this->assertFalse($schemaManager->isPostGis2());
    }

    /**
     * @group postgis-2.x
     */
    public function testIsPostGis2OnPostGIS2x()
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $this->assertTrue($schemaManager->isPostGis2());
    }
}
