<?php

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Test\Functions;

use Jsor\Doctrine\PostGIS\Test\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Test\fixtures\PointsEntity;

class ST_SplitTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
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

    /**
     * @group postgis-2.x
     */
    public function testQuery1Postgis2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Split(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value FROM Jsor\\Doctrine\\PostGIS\\Test\\fixtures\\PointsEntity point');

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
  'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.2454838991936,146.19397662556435 70.86582838174553,141.57348061512727 62.221488349019914,135.3553390593274 54.64466094067266,127.77851165098015 48.42651938487277,119.13417161825454 43.80602337443568,109.75451610080648 40.96073597983849,100.00000000000009 40,90.24548389919367 40.96073597983846,80.86582838174562 43.806023374435625,72.22148834901998 48.426519384872684,64.64466094067271 54.644660940672544,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932726 125.35533905932748,141.57348061512718 117.77851165098022,146.1939766255643 109.1341716182546,149.0392640201615 99.75451610080653,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.42651938487281 62.221488349019786,53.80602337443571 70.86582838174539,50.9607359798385 80.24548389919345,50 89.99999999999984,50.96073597983845 99.75451610080624,53.80602337443559 109.13417161825431,58.42651938487263 117.77851165097995,64.64466094067248 125.35533905932724,72.22148834901971 131.57348061512715,80.86582838174532 136.19397662556426,90.24548389919339 139.03926402016148,99.99999999999982 140,109.75451610080624 139.03926402016157,119.13417161825433 136.1939766255644,127.77851165097998 131.57348061512735,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
];

        $this->assertEquals($expected, $result);
    }

    /**
     * @group postgis-3.x
     */
    public function testQuery1Postgis3()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Split(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value FROM Jsor\\Doctrine\\PostGIS\\Test\\fixtures\\PointsEntity point');

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
            'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.2454838991936,146.19397662556435 70.86582838174553,141.57348061512727 62.221488349019914,135.3553390593274 54.64466094067266,127.77851165098015 48.42651938487277,119.13417161825454 43.80602337443568,109.75451610080648 40.96073597983849,100.00000000000009 40,90.24548389919367 40.96073597983846,80.86582838174562 43.806023374435625,72.22148834901998 48.426519384872684,64.64466094067271 54.644660940672544,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932718 125.35533905932758,141.5734806151271 117.77851165098036,146.19397662556423 109.13417161825477,149.03926402016145 99.75451610080674,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.42651938487281 62.221488349019786,53.80602337443571 70.86582838174539,50.9607359798385 80.24548389919345,50 89.99999999999984,50.96073597983845 99.75451610080624,53.80602337443559 109.13417161825431,58.42651938487263 117.77851165097995,64.64466094067248 125.35533905932724,72.22148834901971 131.57348061512715,80.86582838174532 136.19397662556426,90.24548389919335 139.03926402016148,99.99999999999977 140,109.75451610080616 139.03926402016157,119.13417161825426 136.19397662556443,127.77851165097987 131.57348061512744,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
        ];

        $this->assertEquals($expected, $result);
    }
}
