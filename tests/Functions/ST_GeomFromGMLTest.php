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
final class ST_GeomFromGMLTest extends AbstractFunctionalTestCase
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

    public function testQuery1(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsEWKT(ST_GeomFromGML(ST_AsGML(ST_GeomFromText(\'POLYGON((0 0,0 1,1 1,1 0,0 0))\')))) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value' => 'POLYGON((0 0,0 1,1 1,1 0,0 0))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    public function testQuery2(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsEWKT(ST_GeomFromGML(\'<gml:Polygon srsName="EPSG:4326"><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates>0,0 0,1 1,1 1,0 0,0</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon>\')) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value' => 'SRID=4326;POLYGON((0 0,0 1,1 1,1 0,0 0))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    public function testQuery3(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsEWKT(ST_GeomFromGML(\'<gml:LineString><gml:coordinates>-71.16028,42.258729 -71.160837,42.259112 -71.161143,42.25932</gml:coordinates></gml:LineString>\', 4326)) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value' => 'SRID=4326;LINESTRING(-71.16028 42.258729,-71.160837 42.259112,-71.161143 42.25932)',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
