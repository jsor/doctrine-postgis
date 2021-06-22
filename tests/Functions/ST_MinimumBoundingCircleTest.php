<?php



namespace Jsor\Doctrine\PostGIS\Test\Functions;

use Jsor\Doctrine\PostGIS\Test\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Test\fixtures\PointsEntity;

class ST_MinimumBoundingCircleTest extends AbstractFunctionalTestCase
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

    public function testQuery1()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsText(ST_MinimumBoundingCircle(ST_GeomFromEWKT(\'MULTIPOINT((10 10), (20 20), (10 20), (15 19))\'), 2)) AS value FROM Jsor\\Doctrine\\PostGIS\\Test\\fixtures\\PointsEntity point');

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
  'value' => 'POLYGON((15 22.653668647301796,20.411961001461968 20.41196100146197,22.653668647301796 15,20.41196100146197 9.58803899853803,15.000000000000002 7.346331352698204,9.58803899853803 9.588038998538028,7.346331352698204 14.999999999999998,9.588038998538028 20.411961001461968,14.999999999999998 22.653668647301796))',
];

        $this->assertEquals($expected, $result);
    }
}
