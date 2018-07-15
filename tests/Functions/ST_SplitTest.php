<?php

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\PointsEntity;

/**
 * @group postgis-2.x
 * @group postgis-2.1
 */
class ST_SplitTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Split(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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
  'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.039264020162 80.2454838991936,146.193976625564 70.8658283817455,141.573480615127 62.2214883490199,135.355339059327 54.6446609406727,127.77851165098 48.4265193848728,119.134171618255 43.8060233744357,109.754516100806 40.9607359798385,100 40,90.2454838991937 40.9607359798385,80.8658283817456 43.8060233744356,72.22148834902 48.4265193848727,64.6446609406727 54.6446609406725,60.1371179574584 60.1371179574584,129.862882042542 129.862882042542,135.355339059327 125.355339059327,141.573480615127 117.77851165098,146.193976625564 109.134171618255,149.039264020162 99.7545161008065,150 90)),POLYGON((60.1371179574584 60.1371179574584,58.4265193848728 62.2214883490198,53.8060233744357 70.8658283817454,50.9607359798385 80.2454838991934,50 89.9999999999998,50.9607359798384 99.7545161008062,53.8060233744356 109.134171618254,58.4265193848726 117.77851165098,64.6446609406725 125.355339059327,72.2214883490197 131.573480615127,80.8658283817453 136.193976625564,90.2454838991934 139.039264020161,99.9999999999998 140,109.754516100806 139.039264020162,119.134171618254 136.193976625564,127.77851165098 131.573480615127,129.862882042542 129.862882042542,60.1371179574584 60.1371179574584)))',
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }
}
