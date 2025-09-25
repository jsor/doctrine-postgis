<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

final class SpatialIndexes
{
    /**
     * @return Index[] the spacial indicies
     */
    public static function extractSpatialIndicies(array $indicies): array
    {
        return array_filter($indicies, static fn (Index $idx) => $idx->hasFlag('spatial'));
    }

    /**
     * Ensure the 'spatial' flag is set on PostGIS columns.
     */
    public static function ensureSpatialIndexFlags(Table|TableDiff $table): void
    {
        if ($table instanceof Table) {
            static::applySpatialIndexFlag($table, $table->getIndexes());

            return;
        }

        $tableDiff = $table;
        $table = $tableDiff->getOldTable();
        if (!$table) {
            return;
        }

        static::applySpatialIndexFlag($table, $tableDiff->getAddedIndexes());
        static::applySpatialIndexFlag($table, $tableDiff->getModifiedIndexes());
    }

    /** @return Index[] */
    private static function applySpatialIndexFlag(Table $table, array $indexes): array
    {
        $spatialIndexes = [];

        /** @var Index $index */
        foreach ($indexes as $index) {
            foreach ($index->getColumns() as $columnName) {
                if (!$table->hasColumn($columnName)) {
                    continue;
                }

                $column = $table->getColumn($columnName);
                if ($column->getType() instanceof PostGISType) {
                    if (!$index->hasFlag('spatial')) {
                        $index->addFlag('spatial');
                    }

                    $spatialIndexes[$index->getName()] = $index;
                }
            }
        }

        return array_values($spatialIndexes);
    }
}
