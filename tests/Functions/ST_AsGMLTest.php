<?php

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\PointsEntity;

class ST_AsGMLTest extends AbstractFunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_setUpEntitySchema(array(
            'Jsor\Doctrine\PostGIS\PointsEntity'
        ));

        $em = $this->_getEntityManager();

        $entity = new PointsEntity(array(
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
        ));

        $em->persist($entity);
        $em->flush();
        $em->clear();
    }

    public function testQuery1()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsGML(ST_GeomFromText(\'POLYGON((0 0,0 1,1 1,1 0,0 0))\',4326)) AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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

        $expected = array(
  'value' => '<gml:Polygon srsName="EPSG:4326"><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>0,0 0,1 1,1 1,0 0,0</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon>',
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }

    public function testQuery2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsGML(3, ST_GeomFromText(\'POINT(5.234234233242 6.34534534534)\',4326), 5, 17) AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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

        $expected = array(
  'value' => '<gml:Point srsName="urn:ogc:def:crs:EPSG::4326"><gml:pos srsDimension="2">6.34535 5.23423</gml:pos></gml:Point>',
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }
}
