<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS\Schema;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;

class CreateTableSqlGenerator
{
    private PostgreSqlPlatform $platform;
    private bool $isPostGis2;
    private SpatialColumnSqlGenerator $spatialColumnSqlGenerator;
    private SpatialIndexSqlGenerator $spatialIndexSqlGenerator;

    public function __construct(PostgreSqlPlatform $platform, bool $isPostGis2 = true)
    {
        $this->platform = $platform;
        $this->isPostGis2 = $isPostGis2;
        $this->spatialColumnSqlGenerator = new SpatialColumnSqlGenerator($platform);
        $this->spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($platform);
    }

    public function getSql(Table $table, array $columns, array $options = []): array
    {
        $spatialGeometryColumns = [];

        if (!$this->isPostGis2) {
            $normalColumns = [];

            foreach ($columns as $name => $columnData) {
                if ('geometry' !== $columnData['type']->getName()) {
                    $normalColumns[$name] = $columnData;
                } else {
                    $spatialGeometryColumns[] = $table->getColumn($name);
                }
            }

            $columns = $normalColumns;
        }

        $spatialIndexes = [];

        if (isset($options['indexes']) && !empty($options['indexes'])) {
            $indexes = [];

            foreach ($options['indexes'] as $index) {
                if (!$index->hasFlag('spatial')) {
                    $indexes[] = $index;
                } else {
                    $spatialIndexes[] = $index;
                }
            }

            $options['indexes'] = $indexes;
        }

        $sql = $this->getCreateTableSQL($table, $columns, $options);

        foreach ($spatialGeometryColumns as $column) {
            $sql = array_merge($sql, $this->spatialColumnSqlGenerator->getSql($column, $table));
        }

        foreach ($spatialIndexes as $index) {
            $sql[] = $this->spatialIndexSqlGenerator->getSql($index, $table);
        }

        return $sql;
    }

    public function getCreateTableSQL(Table $table, array $columns, array $options = []): array
    {
        $tableName = $table->getQuotedName($this->platform);

        $sql = $this->_getCreateTableSQL($tableName, $columns, $options);
        if ($this->platform->supportsCommentOnStatement()) {
            foreach ($table->getColumns() as $column) {
                $comment = $this->getColumnComment($column);

                if (null !== $comment && '' !== $comment) {
                    $sql[] = $this->platform->getCommentOnColumnSQL($tableName, $column->getQuotedName($this->platform), $comment);
                }
            }
        }

        return $sql;
    }

    /**
     * Full replacement of Doctrine\DBAL\Platforms\PostgreSqlPlatform::_getCreateTableSQL,
     * check on updates!
     */
    public function _getCreateTableSQL(string $tableName, array $columns, array $options = []): array
    {
        $queryFields = $this->platform->getColumnDeclarationListSQL($columns);

        if (isset($options['primary']) && !empty($options['primary'])) {
            $keyColumns = array_unique(array_values($options['primary']));
            $queryFields .= ', PRIMARY KEY(' . implode(', ', $keyColumns) . ')';
        }

        $query = 'CREATE TABLE ' . $tableName . ' (' . $queryFields . ')';

        $sql[] = $query;

        if (isset($options['indexes']) && !empty($options['indexes'])) {
            foreach ($options['indexes'] as $index) {
                $sql[] = $this->platform->getCreateIndexSQL($index, $tableName);
            }
        }

        if (isset($options['foreignKeys'])) {
            foreach ((array) $options['foreignKeys'] as $definition) {
                $sql[] = $this->platform->getCreateForeignKeySQL($definition, $tableName);
            }
        }

        return $sql;
    }

    /**
     * Full replacement of Doctrine\DBAL\Platforms\AbstractPlatform::getColumnComment,
     * check on updates!
     */
    protected function getColumnComment(Column $column): ?string
    {
        $comment = $column->getComment();

        if ($this->platform->isCommentedDoctrineType($column->getType())) {
            $comment = ($comment ?? '') . $this->platform->getDoctrineTypeComment($column->getType());
        }

        return $comment;
    }
}
