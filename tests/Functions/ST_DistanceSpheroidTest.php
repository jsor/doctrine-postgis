<?php

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;
use function is_resource;

/**
 * @group orm
 * @group functions
 */
class ST_DistanceSpheroidTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_DistanceSpheroid(ST_GeomFromText(\'POINT(-72.1235 42.3521)\', 4326), ST_GeomFromText(\'LINESTRING(-72.1260 42.45, -72.123 42.1546)\', 4326), \'SPHEROID["WGS 84",6378137,298.257223563]\') AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

        $result = $query->getSingleResult();

        array_walk_recursive($result, static function (&$data): void {
            if (is_resource($data)) {
                $data = stream_get_contents($data);

                if (false !== ($pos = strpos($data, 'x'))) {
                    $data = substr($data, $pos + 1);
                }
            }

            $data = (float) $data;
        });

        $expected = [
  'value' => 123.802076746845,
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
