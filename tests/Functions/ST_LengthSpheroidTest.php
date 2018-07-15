<?php

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\PointsEntity;

/**
 * @group postgis-2.x
 * @group postgis-2.2
 */
class ST_LengthSpheroidTest extends AbstractFunctionalTestCase
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

    /**
     * @group postgis-2.x
     */
    public function testQuery1()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_LengthSpheroid(ST_GeomFromText(\'MULTILINESTRING((-118.584 38.374,-118.583 38.5),(-71.05957 42.3589 , -71.061 43))\'),\'SPHEROID["GRS_1980",6378137,298.257222101]\') AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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
  'value' => 85204.5207711805,
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }

    /**
     * @group postgis-1.5
     */
    public function testQuery2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_LengthSpheroid(ST_GeomFromText(\'MULTILINESTRING((-118.584 38.374,-118.583 38.5),(-71.05957 42.3589 , -71.061 43))\'),\'SPHEROID["GRS_1980",6378137,298.257222101]\') AS value FROM Jsor\\Doctrine\\PostGIS\\PointsEntity point');

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
  'value' => 85204.5207562954,
);

        $this->assertEquals($expected, $result, '', 0.0001);
    }
}
