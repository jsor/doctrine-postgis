<?php

namespace Jsor\Doctrine\PostGIS;

use Jsor\Doctrine\PostGIS\DependencyInjection\DoctrineSchemaManagerPass;
use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventSubscriber;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Jsor\Doctrine\PostGIS\Schema\PostGISSchemaManagerFactory;

class JsorDoctrinePostgisBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->register(PostGISSchemaManagerFactory::class, PostGISSchemaManagerFactory::class)
            ->setPublic(true)
        ;
        $container->register('jsor_postgis.orm_schema_subscriber', ORMSchemaEventSubscriber::class)
            ->addTag(
                'doctrine.event_listener',
                [
                    'event'  => 'postGenerateSchemaTable',
                    'method' => 'postGenerateSchemaTable',
                ]
            )
        ;
        $container->addCompilerPass(new DoctrineSchemaManagerPass());
    }
}
