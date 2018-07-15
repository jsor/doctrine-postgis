<?php

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\PointsEntity;

class ST_PerimeterTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_Perimeter(ST_GeomFromText(\'POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))\', 2249)) AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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
  'value' => 122.630744000095,
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }

    /**
     * @group postgis-2.x
     */
    public function testQuery2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_Perimeter(ST_GeomFromText(\'MULTIPOLYGON(((-71.1044543107478 42.340674480411,-71.1044542869917 42.3406744369506,-71.1044553562977 42.340673886454,-71.1044543107478 42.340674480411)),((-71.1044543107478 42.340674480411,-71.1044860600303 42.3407237015564,-71.1045215770124 42.3407653385914,-71.1045498002983 42.3407946553165,-71.1045611902745 42.3408058316308,-71.1046016507427 42.340837442371,-71.104617893173 42.3408475056957,-71.1048586153981 42.3409875993595,-71.1048736143677 42.3409959528211,-71.1048878050242 42.3410084812078,-71.1044020965803 42.3414730072048,-71.1039672113619 42.3412202916693,-71.1037740497748 42.3410666421308,-71.1044280218456 42.3406894151355,-71.1044543107478 42.340674480411)))\'), false) AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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
  'value' => 257.412311446337,
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }
}
