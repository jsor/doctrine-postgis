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
final class ST_TransformTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Transform(ST_GeomFromText(\'POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))\',2249),4326)) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value' => 'POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009,-71.1775844305465 42.3903826677917,-71.177582592723 42.3902893647987,-71.1776848522251 42.3902896512902))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    /**
     * @group postgis-3.1
     * @group versioned
     */
    public function testQuery2(): void
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Transform(ST_GeomFromText(\'POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))\',2249),4326)) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value' => 'POLYGON((-71.1776848522251 42.39028965129018,-71.17768437663261 42.390382947800894,-71.17758443054647 42.39038266779171,-71.17758259272304 42.3902893647987,-71.1776848522251 42.39028965129018))',
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_Transform(ST_GeomFromText(\'POLYGON((743238 2967416,743238 2967450,743265 2967450,743265.625 2967416,743238 2967416))\',2249),4326)) AS value FROM Jsor\\Doctrine\\PostGIS\\Entity\\PointsEntity point');

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
  'value' => 'POLYGON((-71.1776848522251 42.3902896512903,-71.17768437663261 42.390382947801015,-71.17758443054647 42.390382667791854,-71.17758259272304 42.390289364798825,-71.1776848522251 42.3902896512903))',
];

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
}
