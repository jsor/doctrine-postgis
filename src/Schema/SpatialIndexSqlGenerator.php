<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;
use function count;

class SpatialIndexSqlGenerator
{
    private AbstractPlatform $platform;

    public function __construct(AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }

    public function getSql(Index $index, $table): string
    {
        if ($table instanceof Table) {
            $table = $table->getQuotedName($this->platform);
        }

        $name = $index->getQuotedName($this->platform);
        $columns = $index->getQuotedColumns($this->platform);

        if (0 === count($columns)) {
            throw new InvalidArgumentException("Incomplete definition. 'columns' required.");
        }

        if ($index->isPrimary()) {
            return $this->platform->getCreatePrimaryKeySQL($index, $table);
        }

        $query = 'CREATE INDEX ' . $name . ' ON ' . $table;
        $query .= ' USING gist(' . $this->platform->getIndexFieldDeclarationListSQL($columns) . ')';

        return $query;
    }
}
