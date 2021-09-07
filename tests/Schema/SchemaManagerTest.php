<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;

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
        $schemaManager = new SchemaManager($this->_getConnection());

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
        $schemaManager = new SchemaManager($this->_getConnection());

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
        $schemaManager = new SchemaManager($this->_getConnection());

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
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = [
            'type' => 'GEOMETRY',
            'srid' => 0,
        ];
        $this->assertEquals($expected, $schemaManager->getGeometrySpatialColumnInfo('"user"', '"user"'));
    }

    public function testGetGeographySpatialColumnInfoWithReservedWords(): void
    {
        $schemaManager = new SchemaManager($this->_getConnection());

        $expected = [
            'type' => 'GEOMETRY',
            'srid' => 4326,
        ];
        $this->assertEquals($expected, $schemaManager->getGeographySpatialColumnInfo('"user"', '"primary"'));
    }
}
