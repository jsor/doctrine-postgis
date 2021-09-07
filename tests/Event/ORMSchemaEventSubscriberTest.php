<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Event;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;
use Jsor\Doctrine\PostGIS\Entity\ReservedWordsEntity;

/**
 * @group orm
 */
final class ORMSchemaEventSubscriberTest extends AbstractFunctionalTestCase
{
    public function testEntity(): void
    {
        $this->_setUpEntitySchema([
            PointsEntity::class,
        ]);

        $em = $this->_getEntityManager();

        $sm = $em->getConnection()->getSchemaManager();
        $table = $sm->listTableDetails('points');
        $this->assertTrue($table->hasIndex('idx_point'));
        $this->assertTrue($table->getIndex('idx_point')->hasFlag('spatial'));

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

        $entity = $em->find(PointsEntity::class, 1);

        $this->assertEquals('POINT(1 1)', $entity->getPoint());
        $this->assertEquals('SRID=3785;POINT(1 1)', $entity->getPoint2d());
        $this->assertEquals('SRID=3785;POINT(1 1 1)', $entity->getPoint3dz());
        $this->assertEquals('SRID=3785;POINTM(1 1 1)', $entity->getPoint3dm());
        $this->assertEquals('SRID=3785;POINT(1 1 1 1)', $entity->getPoint4d());
        $this->assertNull($entity->getPoint2dNullable());
        $this->assertEquals('POINT(1 1)', $entity->getPoint2dNoSrid());
        $this->assertEquals('SRID=4326;POINT(1 1)', $entity->getPointGeography2d());
        $this->assertEquals('SRID=4326;POINT(1 1)', $entity->getPointGeography2dSrid());
    }

    public function testEntityWithReservedWords(): void
    {
        $this->_setUpEntitySchema([
            ReservedWordsEntity::class,
        ]);

        $em = $this->_getEntityManager();

        $entity = new ReservedWordsEntity([
            'user' => 'POINT(1 1)',
            'primary' => 'SRID=4326;POINT(1 1)',
        ]);

        $em->persist($entity);
        $em->flush();
        $em->clear();

        $entity = $em->find(ReservedWordsEntity::class, 1);

        $this->assertEquals('POINT(1 1)', $entity->getUser());
        $this->assertEquals('SRID=4326;POINT(1 1)', $entity->getPrimary());
    }
}
