<?php

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Test\Functions;

use Jsor\Doctrine\PostGIS\Test\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Test\fixtures\PointsEntity;

class ST_NumGeometriesTest extends AbstractFunctionalTestCase
{
    protected function setUp():void
    {
        parent::setUp();

        $this->_setUpEntitySchema([
            'Jsor\Doctrine\PostGIS\Test\fixtures\PointsEntity'
        ]);

        $em = $this->_getEntityManager();

        $entity = new PointsEntity([
            'text' => 'foo',
            'geometry' => 'POINT(1 1)',
            'point' => 'POINT(1 1)',
            'point2D' => 'SRID=3785;POINT(1 1)',
            'point3DZ' => 'SRID=3785;POINT(1 1 1)',
            'point3DM' => 'SRID=3785;POINTM(1 1 1)',
            'point4D' => 'SRID=3785;POINT(1 1 1 1)',
            'point2DNullable' => null,
            'point2DNoSrid' => 'POINT(1 1)',
            'geography' => 'SRID=4326;POINT(1 1)',
            'pointGeography2d' => 'SRID=4326;POINT(1 1)',
            'pointGeography2dSrid' => 'POINT(1 1)',
        ]);

        $em->persist($entity);
        $em->flush();
        $em->clear();
    }

    public function testQuery2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_NumGeometries(ST_GeomFromText(\'LINESTRING(77.29 29.07,77.42 29.26,77.27 29.31,77.29 29.07)\')) AS value FROM Jsor\\Doctrine\\PostGIS\\Test\\fixtures\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, function (&$data) {
            if (is_resource($data)) {
                $data = stream_get_contents($data);

                if (false !== ($pos = strpos($data, 'x'))) {
                    $data = substr($data, $pos + 1);
                }
            }

            if (is_string($data)) {
                $data = trim($data);
            }
        });

        $expected = [
  'value' => 1,
];

        $this->assertEquals($expected, $result, '', 0.0001);
    }

    public function testQuery3()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_NumGeometries(ST_GeomFromText(\'GEOMETRYCOLLECTION(MULTIPOINT(-2 3 , -2 2),LINESTRING(5 5 ,10 10),POLYGON((-7 4.2,-7.1 5,-7.1 4.3,-7 4.2)))\')) AS value FROM Jsor\\Doctrine\\PostGIS\\Test\\fixtures\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, function (&$data) {
            if (is_resource($data)) {
                $data = stream_get_contents($data);

                if (false !== ($pos = strpos($data, 'x'))) {
                    $data = substr($data, $pos + 1);
                }
            }

            if (is_string($data)) {
                $data = trim($data);
            }
        });

        $expected = [
  'value' => 3,
];

        $this->assertEquals($expected, $result, '', 0.0001);
    }
}
