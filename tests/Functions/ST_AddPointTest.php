<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;

use function is_resource;
use function is_string;

/**
 * @covers \Jsor\Doctrine\PostGIS\Functions\ST_AddPoint
 *
 * @group orm
 * @group functions
 */
final class ST_AddPointTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_AddPoint(ST_GeomFromText(\'LINESTRING(1.1115678 2.123, 4.111111 3.2374897, 4.11112 3.23748667)\', 4326), ST_GeomFromText(\'POINT(-123.365556 48.428611)\', 4326))) as value1, ST_AsText(ST_AddPoint(ST_GeomFromText(\'LINESTRING(1.1115678 2.123, 4.111111 3.2374897, 4.11112 3.23748667)\', 4326), ST_GeomFromText(\'POINT(-123.365556 48.428611)\', 4326), 1)) AS value2 FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
            'value1' => 'LINESTRING(1.1115678 2.123,4.111111 3.2374897,4.11112 3.23748667,-123.365556 48.428611)',
            'value2' => 'LINESTRING(1.1115678 2.123,-123.365556 48.428611,4.111111 3.2374897,4.11112 3.23748667)',
        ];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
