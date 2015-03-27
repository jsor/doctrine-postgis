<?php

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Jsor\Doctrine\PostGIS\Query\AST\Functions\Configurator;

class ORMSchemaEventSubscriber extends DBALSchemaEventSubscriber
{
    public function getSubscribedEvents()
    {
        return array_merge(
            parent::getSubscribedEvents(),
            array(
                ToolEvents::postGenerateSchemaTable,
            )
        );
    }

    public function postConnect(ConnectionEventArgs $args)
    {
        parent::postConnect($args);

        $configuration = $args->getConnection()->getConfiguration();

        // Check if ORM and DBAL share a Doctrine\ORM\Configuration instance
        if ($configuration instanceof Configuration) {
            Configurator::configure($configuration);
        }
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args)
    {
        $table = $args->getClassTable();

        foreach ($table->getColumns() as $column) {
            if (!$this->isSpatialColumnType($column)) {
                continue;
            }

            $normalized = $column->getType()->getNormalizedSpatialOptions(
                $column->getCustomSchemaOptions()
            );

            foreach ($normalized as $name => $value) {
                $column->setCustomSchemaOption($name, $value);
            }
        }

        // Add SPATIAL flags to indexes
        if ($table->hasOption('spatial_indexes')) {
            foreach ((array) $table->getOption('spatial_indexes') as $indexName) {
                if (!$table->hasIndex($indexName)) {
                    continue;
                }

                $table->getIndex($indexName)->addFlag('SPATIAL');
            }
        }
    }
}
