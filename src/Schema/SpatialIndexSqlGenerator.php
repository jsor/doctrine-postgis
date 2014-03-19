<?php

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;

class SpatialIndexSqlGenerator
{
    private $platform;

    public function __construct(PostgreSqlPlatform $platform)
    {
        $this->platform = $platform;
    }

    public function getSql(Index $index, $table)
    {
        if ($table instanceof Table) {
            $table = $table->getQuotedName($this->platform);
        }

        $name = $index->getQuotedName($this->platform);
        $columns = $index->getQuotedColumns($this->platform);

        if (count($columns) == 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'columns' required.");
        }

        if ($index->isPrimary()) {
            return $this->platform->getCreatePrimaryKeySQL($index, $table);
        }

        $query = 'CREATE INDEX ' . $name . ' ON ' . $table;
        $query .= ' USING gist(' . $this->platform->getIndexFieldDeclarationListSQL($columns) . ')';

        return $query;
    }
}
