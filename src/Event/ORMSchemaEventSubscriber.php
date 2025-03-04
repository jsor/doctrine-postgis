<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\ORM\Tools\Event\GenerateSchemaTableEventArgs;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

class ORMSchemaEventSubscriber
{
    public function postGenerateSchemaTable(GenerateSchemaTableEventArgs $args): void
    {
        $table = $args->getClassTable();

        foreach ($table->getColumns() as $column) {
            $type = $column->getType();

            if (!$type instanceof PostGISType) {
                continue;
            }

            /** @var array{geometry_type?: string|null, srid?: int|string|null} $options */
            $options = $column->getPlatformOptions();

            $normalized = $type->getNormalizedPostGISColumnOptions($options);

            foreach ($normalized as $name => $value) {
                $column->setPlatformOption($name, $value);
            }
        }
    }
}
