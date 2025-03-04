<?php

namespace Jsor\Doctrine\PostGIS\DependencyInjection;

use Jsor\Doctrine\PostGIS\Schema\PostGISSchemaManagerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineSchemaManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->register('app.postgis_schema_manager_factory', PostGISSchemaManagerFactory::class)
            ->setPublic(true)
        ;

        foreach ($container->findTaggedServiceIds('doctrine.dbal.connection') as $id => $tags) {
            $connectionDef = $container->getDefinition($id);
            $params        = $connectionDef->getArgument(0);

            if (is_array($params)) {
                $params['schema_manager_factory'] = new Reference('app.postgis_schema_manager_factory');
                $connectionDef->replaceArgument(0, $params);
            }
        }

        $doctrinePrefix = 'doctrine.dbal.default_connection.';

        if ($container->hasParameter($doctrinePrefix . 'configuration')) {
            $config = $container->getParameter($doctrinePrefix . 'configuration');

            if (is_array($config)) {
                $config['schema_manager_factory'] = 'app.postgis_schema_manager_factory';

                $container->setParameter($doctrinePrefix . 'configuration', $config);
            }
        }
    }
}
