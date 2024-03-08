<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Jsor\Doctrine\PostGIS\Types\PostGISType;
use Jsor\Doctrine\PostGIS\Utils\Doctrine;

final class SpatialIndexes
{
    /**
     * @return Index[] the spacial indicies
     */
    public static function extractSpatialIndicies(array $indicies): array
    {
        return array_filter($indicies, static fn (Index $idx) => $idx->hasFlag('spatial'));
    }

    public static function filterSchemaDiff(SchemaDiff $schemaDiff): SchemaDiff
    {
        $filter = static fn (TableDiff $diff): TableDiff => self::filterTableDiff($diff);

        if (Doctrine::isV3()) {
            return new SchemaDiff(
                $schemaDiff->getCreatedTables(),
                array_map($filter, $schemaDiff->getAlteredTables()),
                $schemaDiff->getDroppedTables(),
                $schemaDiff->fromSchema,
                $schemaDiff->getCreatedSchemas(),
                $schemaDiff->getDroppedSchemas(),
                $schemaDiff->getCreatedSequences(),
                $schemaDiff->getAlteredSequences(),
                $schemaDiff->getDroppedSequences(),
            );
        }

        return new SchemaDiff(
            $schemaDiff->getCreatedSchemas(),
            $schemaDiff->getDroppedSchemas(),
            $schemaDiff->getCreatedTables(),
            array_map($filter, $schemaDiff->getAlteredTables()),
            $schemaDiff->getDroppedTables(),
            $schemaDiff->getCreatedSequences(),
            $schemaDiff->getAlteredSequences(),
            $schemaDiff->getDroppedSequences(),
        );
    }

    /**
     * Filter spatial indexes from a TableDiff to prevent duplicate index SQL generation.
     */
    public static function filterTableDiff(TableDiff $tableDiff): TableDiff
    {
        $spatialFilter = static fn (Index $idx): bool => !$idx->hasFlag('spatial');

        if (Doctrine::isV3()) {
            return new TableDiff(
                (string) $tableDiff->getOldTable()?->getName(),
                $tableDiff->getAddedColumns(),
                $tableDiff->getModifiedColumns(),
                $tableDiff->getDroppedColumns(),
                array_filter($tableDiff->getAddedIndexes(), $spatialFilter),
                array_filter($tableDiff->getModifiedIndexes(), $spatialFilter),
                $tableDiff->getDroppedIndexes(),
                $tableDiff->getOldTable(),
                $tableDiff->getAddedForeignKeys(),
                $tableDiff->getModifiedForeignKeys(),
                $tableDiff->getDroppedForeignKeys(),
                $tableDiff->getRenamedColumns(),
                $tableDiff->getRenamedIndexes(),
            );
        }

        return new TableDiff(
            $tableDiff->getOldTable(),
            $tableDiff->getAddedColumns(),
            $tableDiff->getModifiedColumns(),
            $tableDiff->getDroppedColumns(),
            $tableDiff->getRenamedColumns(),
            array_filter($tableDiff->getAddedIndexes(), $spatialFilter),
            array_filter($tableDiff->getModifiedIndexes(), $spatialFilter),
            $tableDiff->getDroppedIndexes(),
            $tableDiff->getRenamedIndexes(),
            $tableDiff->getAddedForeignKeys(),
            $tableDiff->getModifiedForeignKeys(),
            $tableDiff->getDroppedForeignKeys(),
        );
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
