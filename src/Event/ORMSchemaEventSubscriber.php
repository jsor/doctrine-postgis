<?php

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

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

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args)
    {
        $table = $args->getClassTable();

        foreach ($table->getColumns() as $column) {
            if (!$this->isSpatialColumnType($column)) {
                continue;
            }

            $normalized = $column->getType()->getNormalizedPostGISColumnOptions(
                $column->getCustomSchemaOptions()
            );

            foreach ($normalized as $name => $value) {
                $column->setCustomSchemaOption($name, $value);
            }
        }

        // Add spatial flags to indexes
        if ($table->hasOption('spatial_indexes')) {
            foreach ((array) $table->getOption('spatial_indexes') as $indexName) {
                if (!$table->hasIndex($indexName)) {
                    continue;
                }

                $table->getIndex($indexName)->addFlag('spatial');
            }
        }
    }
}
