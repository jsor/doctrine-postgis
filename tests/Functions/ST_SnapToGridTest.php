<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;

use function is_resource;
use function is_string;

/**
 * @covers \Jsor\Doctrine\PostGIS\Functions\ST_SnapToGrid
 *
 * @group orm
 * @group functions
 */
final class ST_SnapToGridTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_SnapToGrid(ST_GeomFromText(\'LINESTRING(1.1115678 2.123, 4.111111 3.2374897, 4.11112 3.23748667)\'),0.001)) as value1, ST_AsEWKT(ST_SnapToGrid(ST_GeomFromEWKT(\'LINESTRING(-1.1115678 2.123 2.3456 1.11111, 4.111111 3.2374897 3.1234 1.1111, -1.11111112 2.123 2.3456 1.1111112)\'), ST_GeomFromEWKT(\'POINT(1.12 2.22 3.2 4.4444)\'), 0.1, 0.1, 0.1, 0.01)) as value2, ST_AsEWKT(ST_SnapToGrid(ST_GeomFromEWKT(\'LINESTRING(-1.1115678 2.123 3 2.3456, 4.111111 3.2374897 3.1234 1.1111)\'), 0.01)) AS value3 FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value1' => 'LINESTRING(1.112 2.123,4.111 3.237)',
  'value2' => 'LINESTRING(-1.08 2.12 2.3 1.1144,4.12 3.22 3.1 1.1144,-1.08 2.12 2.3 1.1144)',
  'value3' => 'LINESTRING(-1.11 2.12 3 2.3456,4.11 3.24 3.1234 1.1111)',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
