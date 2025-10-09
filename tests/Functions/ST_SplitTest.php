<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;

use function is_resource;
use function is_string;

/**
 * @covers \Jsor\Doctrine\PostGIS\Functions\ST_Split
 *
 * @group orm
 * @group functions
 */
final class ST_SplitTest extends AbstractFunctionalTestCase
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
     * @group postgis-3.2
     * @group postgis-3.4
     * @group versioned
     */
    public function testQuery1(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Split(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
            'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,99.99999999999999 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
        ];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    /**
     * @group postgis-3.6
     * @group versioned
     */
    public function testQuery2(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Split(ST_Buffer(ST_GeomFromText(\'POINT(100 90)\'), 50), ST_MakeLine(ST_MakePoint(10, 10),ST_MakePoint(190, 190)))) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
            'value' => 'GEOMETRYCOLLECTION(POLYGON((150 90,149.0392640201615 80.24548389919359,146.19397662556435 70.86582838174552,141.57348061512727 62.22148834901989,135.35533905932738 54.64466094067263,127.77851165098011 48.42651938487274,119.1341716182545 43.80602337443566,109.75451610080641 40.960735979838475,100 40,90.24548389919359 40.960735979838475,80.86582838174552 43.80602337443566,72.2214883490199 48.426519384872734,64.64466094067262 54.64466094067262,60.13711795745844 60.13711795745844,129.86288204254154 129.86288204254154,135.35533905932738 125.35533905932738,141.57348061512727 117.77851165098011,146.19397662556432 109.13417161825453,149.0392640201615 99.75451610080644,150 90)),POLYGON((60.13711795745844 60.13711795745844,58.426519384872734 62.22148834901989,53.80602337443566 70.8658283817455,50.960735979838475 80.24548389919357,50 90,50.960735979838475 99.75451610080641,53.80602337443566 109.13417161825448,58.42651938487273 117.7785116509801,64.64466094067262 125.35533905932738,72.22148834901989 131.57348061512727,80.86582838174549 136.19397662556432,90.24548389919357 139.0392640201615,100 140,109.75451610080641 139.0392640201615,119.1341716182545 136.19397662556435,127.7785116509801 131.57348061512727,129.86288204254154 129.86288204254154,60.13711795745844 60.13711795745844)))',
        ];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
