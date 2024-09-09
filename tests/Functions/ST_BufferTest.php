<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;

use function is_resource;
use function is_string;

/**
 * @group orm
 * @group functions
 */
final class ST_BufferTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->_setUpEntitySchema([
            PointsEntity::class,
        ]);

        $em = $this->_getEntityManager();

        $entity = new PointsEntity([
            'text' => 'foo',
            'geometry' => 'POINT(1 1)',
            'point' => 'POINT(1 1)',
            'point2d' => 'SRID=3785;POINT(1 1)',
            'point3dz' => 'SRID=3785;POINT(1 1 1)',
            'point3dm' => 'SRID=3785;POINTM(1 1 1)',
            'point4d' => 'SRID=3785;POINT(1 1 1 1)',
            'point2dNullable' => null,
            'point2dNoSrid' => 'POINT(1 1)',
            'geography' => 'SRID=4326;POINT(1 1)',
            'pointGeography2d' => 'SRID=4326;POINT(1 1)',
            'pointGeography2dSrid' => 'POINT(1 1)',
        ]);

        $em->persist($entity);
        $em->flush();
        $em->clear();
    }

    /**
     * @group postgis-3.0
     * @group versioned
     */
    public function testQuery1(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50, \'quad_segs=8\')) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, static function (&$data): void {
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
  'value' => 'POLYGON((150 90,149.039264020162 80.2454838991936,146.193976625564 70.8658283817455,141.573480615127 62.2214883490199,135.355339059327 54.6446609406727,127.77851165098 48.4265193848728,119.134171618255 43.8060233744357,109.754516100806 40.9607359798385,100 40,90.2454838991937 40.9607359798385,80.8658283817456 43.8060233744356,72.22148834902 48.4265193848727,64.6446609406727 54.6446609406725,58.4265193848728 62.2214883490198,53.8060233744357 70.8658283817454,50.9607359798385 80.2454838991934,50 89.9999999999998,50.9607359798384 99.7545161008062,53.8060233744356 109.134171618254,58.4265193848726 117.77851165098,64.6446609406725 125.355339059327,72.2214883490197 131.573480615127,80.8658283817453 136.193976625564,90.2454838991934 139.039264020161,99.9999999999998 140,109.754516100806 139.039264020162,119.134171618254 136.193976625564,127.77851165098 131.573480615127,135.355339059327 125.355339059327,141.573480615127 117.77851165098,146.193976625564 109.134171618255,149.039264020162 99.7545161008065,150 90))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    /**
     * @group postgis-3.1
     * @group versioned
     */
    public function testQuery2(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50, \'quad_segs=8\')) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, static function (&$data): void {
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
  'value' => 'POLYGON((150 90,149.0392640201615 80.2454838991936,146.19397662556435 70.86582838174553,141.57348061512727 62.221488349019914,135.3553390593274 54.64466094067266,127.77851165098015 48.42651938487277,119.13417161825454 43.80602337443568,109.75451610080648 40.96073597983849,100.00000000000009 40,90.24548389919367 40.96073597983846,80.86582838174562 43.806023374435625,72.22148834901998 48.426519384872684,64.64466094067271 54.644660940672544,58.42651938487281 62.221488349019786,53.80602337443571 70.86582838174539,50.9607359798385 80.24548389919345,50 89.99999999999984,50.96073597983845 99.75451610080624,53.80602337443559 109.13417161825431,58.42651938487263 117.77851165097995,64.64466094067248 125.35533905932724,72.22148834901971 131.57348061512715,80.86582838174532 136.19397662556426,90.24548389919335 139.03926402016148,99.99999999999977 140,109.75451610080616 139.03926402016157,119.13417161825426 136.19397662556443,127.77851165097987 131.57348061512744,135.35533905932718 125.35533905932758,141.5734806151271 117.77851165098036,146.19397662556423 109.13417161825477,149.03926402016145 99.75451610080674,150 90))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    /**
     * @group postgis-3.2
     * @group postgis-3.3
     * @group postgis-3.4
     * @group versioned
     */
    public function testQuery3(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50, \'quad_segs=8\')) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, static function (&$data): void {
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
  'value' => 'POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,99.99999999999999 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    public function testQuery4(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_NPoints(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50)) AS promisingcircle_pcount, ST_NPoints(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50, 2)) AS lamecircle_pcount FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, static function (&$data): void {
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
  'promisingcircle_pcount' => 33,
  'lamecircle_pcount' => 9,
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
