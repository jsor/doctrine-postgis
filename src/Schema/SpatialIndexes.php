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
     * Filter spatial indexes from a TableDiff to prevent duplicate index SQL generation.
     */
    public static function filterTableDiff(TableDiff $tableDiff): void
    {
        $tableDiff->addedIndexes = array_filter($tableDiff->addedIndexes, static fn (Index $idx) => !$idx->hasFlag('spatial'));

        $changedIndexes = [];
        /** @var Index $index */
        foreach ($tableDiff->changedIndexes as $index) {
            if ($index->hasFlag('spatial')) {
                $tableDiff->removedIndexes[] = $index;
            } else {
                $changedIndexes[] = $index;
            }
        }
        $tableDiff->changedIndexes = $changedIndexes;
    }

    /**
     * Ensure the 'spatial' flag is set on PostGIS columns in a Table.
     *
     * @return Index[] the spacial indicies
     */
    public static function ensureTableFlag(Table $table): array
    {
        return static::ensureFlag($table, $table->getIndexes());
    }

    /**
     * Ensure the 'spatial' flag is set on PostGIS columns in a TableDiff.
     *
     * @return Index[] the spacial indicies
     */
    public static function ensureTableDiffFlag(TableDiff $tableDiff): array
    {
        $table = $tableDiff->getOldTable();
        if (!$table) {
            return [];
        }

        $addedSpatialIndexes = static::ensureFlag($table, $tableDiff->getAddedIndexes());

        $modifiedSpatialIndexes = static::ensureFlag($table, $tableDiff->getModifiedIndexes());

        return array_merge($addedSpatialIndexes, $modifiedSpatialIndexes);
    }

    /** @return Index[] */
    private static function ensureFlag(Table $table, array $indexes): array
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
