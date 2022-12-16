<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;

use function count;

final class SpatialIndexSqlGenerator
{
    private AbstractPlatform $platform;

    public function __construct(AbstractPlatform $platform)
    {
        $this->platform = $platform;
    }

    public function getSql(Index $index, Table|Identifier $table): string
    {
        $columns = $index->getQuotedColumns($this->platform);

        if (0 === count($columns)) {
            throw new InvalidArgumentException("Incomplete definition. 'columns' required.");
        }

        $tableName = $table->getQuotedName($this->platform);

        if ($index->isPrimary()) {
            return $this->platform->getCreatePrimaryKeySQL($index, $tableName);
        }

        $name = $index->getQuotedName($this->platform);

        $query = 'CREATE INDEX ' . $name . ' ON ' . $tableName;
        $query .= ' USING gist(' . implode(', ', $index->getQuotedColumns($this->platform)) . ')';

        return $query;
    }
}
