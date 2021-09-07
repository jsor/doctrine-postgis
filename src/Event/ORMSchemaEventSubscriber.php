<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Doctrine\ORM\Tools\ToolEvents;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

class ORMSchemaEventSubscriber extends DBALSchemaEventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return array_merge(
            parent::getSubscribedEvents(),
            [
                ToolEvents::postGenerateSchemaTable,
            ]
        );
    }

    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args): void
    {
        $table = $args->getClassTable();

        foreach ($table->getColumns() as $column) {
            if (!$column->getType() instanceof PostGISType) {
                continue;
            }

            /** @var PostGISType $type */
            $type = $column->getType();

            /** @var array{primary?: array<string>, indexes?: array<Index>, foreignKeys?: ForeignKeyConstraint|array<ForeignKeyConstraint>} $options */
            $options = $column->getCustomSchemaOptions();

            $normalized = $type->getNormalizedPostGISColumnOptions($options);

            foreach ($normalized as $name => $value) {
                $column->setCustomSchemaOption($name, $value);
            }
        }
    }
}
