<?php

namespace Jsor\Doctrine\PostGIS\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Event\SchemaAlterTableAddColumnEventArgs;
use Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use Doctrine\DBAL\Event\SchemaAlterTableEventArgs;
use Doctrine\DBAL\Event\SchemaAlterTableRemoveColumnEventArgs;
use Doctrine\DBAL\Event\SchemaAlterTableRenameColumnEventArgs;
use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Event\SchemaCreateTableEventArgs;
use Doctrine\DBAL\Event\SchemaDropTableEventArgs;
use Doctrine\DBAL\Event\SchemaIndexDefinitionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Type;
use Jsor\Doctrine\PostGIS\Schema\CreateTableSqlGenerator;
use Jsor\Doctrine\PostGIS\Schema\SchemaManager;
use Jsor\Doctrine\PostGIS\Schema\SpatialColumnSqlGenerator;
use Jsor\Doctrine\PostGIS\Schema\SpatialIndexSqlGenerator;
use Jsor\Doctrine\PostGIS\Types\PostGISType;

class DBALSchemaEventSubscriber implements EventSubscriber
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var \Jsor\Doctrine\PostGIS\Schema\SchemaManager
     */
    protected $schemaManager;

    /**
     * @var bool
     */
    protected $postConnectCalled = false;

    public function getSubscribedEvents()
    {
        return array(
            Events::postConnect,
            Events::onSchemaCreateTable,
            Events::onSchemaDropTable,
            Events::onSchemaColumnDefinition,
            Events::onSchemaIndexDefinition,
            Events::onSchemaAlterTable,
            Events::onSchemaAlterTableAddColumn,
            Events::onSchemaAlterTableRemoveColumn,
            Events::onSchemaAlterTableChangeColumn,
            Events::onSchemaAlterTableRenameColumn,
        );
    }

    public function postConnect(ConnectionEventArgs $args)
    {
        if ($this->postConnectCalled) {
            // Allows multiple postConnect calls for the same connection
            // instance. This is done by MasterSlaveConnection for example when
            // switching master/slave connections.
            if ($this->connection === $args->getConnection()) {
                return;
            }

            throw new \LogicException(
                sprintf(
                    'It looks like you have registered the %s to more than one connection. Please register one instance per connection.',
                    get_class($this)
                )
            );
        }

        $this->connection = $args->getConnection();
        $this->schemaManager = new SchemaManager($this->connection);
        $this->postConnectCalled = true;

        if (!Type::hasType('geometry')) {
            Type::addType('geometry', 'Jsor\Doctrine\PostGIS\Types\GeometryType');
        }

        if (!Type::hasType('geography')) {
            Type::addType('geography', 'Jsor\Doctrine\PostGIS\Types\GeographyType');
        }

        if (!Type::hasType('raster')) {
            Type::addType('raster', 'Jsor\Doctrine\PostGIS\Types\RasterType');
        }
    }

    public function onSchemaCreateTable(SchemaCreateTableEventArgs $args)
    {
        $generator = new CreateTableSqlGenerator(
            $args->getPlatform(),
            $this->schemaManager->isPostGis2()
        );

        $args
            ->addSql(
                $generator->getSql(
                    $args->getTable(),
                    $args->getColumns(),
                    $args->getOptions()
                )
            )
            ->preventDefault()
        ;
    }

    public function onSchemaDropTable(SchemaDropTableEventArgs $args)
    {
        if ($this->schemaManager->isPostGis2()) {
            return;
        }

        $table = $args->getTable();
        $hasSpatialGeometryColumn = count($this->schemaManager->listSpatialGeometryColumns($table->getName())) > 0;

        if ($hasSpatialGeometryColumn) {
            $args
                ->setSql("SELECT DropGeometryTable('" . $table->getName() . "')")
                ->preventDefault();
        }
    }

    public function onSchemaAlterTable(SchemaAlterTableEventArgs $args)
    {
        $platform = $args->getPlatform();
        $diff = $args->getTableDiff();

        $spatialIndexes = array();
        $addedIndexes = array();
        $changedIndexes = array();

        foreach ($diff->addedIndexes as $index) {
            if (!$index->hasFlag('spatial')) {
                $addedIndexes[] = $index;
            } else {
                $spatialIndexes[] = $index;
            }
        }

        foreach ($diff->changedIndexes as $index) {
            if (!$index->hasFlag('spatial')) {
                $changedIndexes[] = $index;
            } else {
                $diff->removedIndexes[] = $index;
                $spatialIndexes[] = $index;
            }
        }

        $diff->addedIndexes = $addedIndexes;
        $diff->changedIndexes = $changedIndexes;

        $spatialIndexSqlGenerator = new SpatialIndexSqlGenerator($platform);
        $sql = array();

        $table = new Identifier(false !== $diff->newName ? $diff->newName : $diff->name);
        $tableName = $table->getQuotedName($platform);

        foreach ($spatialIndexes as $index) {
            $sql[] = $spatialIndexSqlGenerator->getSql($index, $tableName);
        }

        $args
            ->addSql($sql)
        ;
    }

    public function onSchemaAlterTableAddColumn(SchemaAlterTableAddColumnEventArgs $args)
    {
        $column = $args->getColumn();

        if (!$this->isSpatialColumnType($column)) {
            return;
        }

        if ('geometry' !== $column->getType()->getName() ||
            $this->schemaManager->isPostGis2()) {
            return;
        }

        $diff = $args->getTableDiff();
        $table = false !== $diff->newName ? $diff->newName : $diff->name;

        $spatialColumnSqlGenerator = new SpatialColumnSqlGenerator($args->getPlatform());

        $args
            ->addSql($spatialColumnSqlGenerator->getSql($column, $table))
            ->preventDefault()
        ;
    }

    public function onSchemaAlterTableRemoveColumn(SchemaAlterTableRemoveColumnEventArgs $args)
    {
        $column = $args->getColumn();

        if (!$this->isSpatialColumnType($column)) {
            return;
        }

        if ('geometry' !== $column->getType()->getName() ||
            $this->schemaManager->isPostGis2()) {
            return;
        }

        $platform = $args->getPlatform();

        $diff = $args->getTableDiff();
        $table = new Identifier(false !== $diff->newName ? $diff->newName : $diff->name);

        if ($column->getNotnull()) {
            // Remove NOT NULL constraint from the field
            $args->addSql(sprintf(
                'ALTER TABLE %s ALTER %s SET DEFAULT NULL',
                $table->getQuotedName($platform),
                $column->getQuotedName($platform)
            ));
        }

        // We use DropGeometryColumn() to also drop entries from the geometry_columns table
        $args->addSql(sprintf(
            "SELECT DropGeometryColumn('%s', '%s')",
            $table->getName(),
            $column->getName()
        ));

        $args
            ->preventDefault();
    }

    public function onSchemaAlterTableChangeColumn(SchemaAlterTableChangeColumnEventArgs $args)
    {
        $columnDiff = $args->getColumnDiff();
        $column = $columnDiff->column;

        if (!$this->isSpatialColumnType($column)) {
            return;
        }

        $diff = $args->getTableDiff();
        $table = new Identifier(false !== $diff->newName ? $diff->newName : $diff->name);

        if ($columnDiff->hasChanged('type')) {
            throw new \RuntimeException('The type of a spatial column cannot be changed (Requested changing type from "' . $columnDiff->fromColumn->getType()->getName() . '" to "' . $column->getType()->getName() . '" for column "' . $column->getName() . '" in table "' . $table->getName() . '")');
        }

        if ($columnDiff->hasChanged('geometry_type')) {
            throw new \RuntimeException('The geometry_type of a spatial column cannot be changed (Requested changing type from "' . strtoupper($columnDiff->fromColumn->getCustomSchemaOption('geometry_type')) . '" to "' . strtoupper($column->getCustomSchemaOption('geometry_type')) . '" for column "' . $column->getName() . '" in table "' . $table->getName() . '")');
        }

        if ($columnDiff->hasChanged('srid')) {
            $args->addSql(sprintf(
                "SELECT UpdateGeometrySRID('%s', '%s', %d)",
                $table->getName(),
                $column->getName(),
                $column->getCustomSchemaOption('srid')
            ));
        }
    }

    public function onSchemaAlterTableRenameColumn(SchemaAlterTableRenameColumnEventArgs $args)
    {
        $column = $args->getColumn();

        if (!$this->isSpatialColumnType($column)) {
            return;
        }

        if ($this->schemaManager->isPostGis2()) {
            return;
        }

        throw new \RuntimeException('Spatial columns cannot be renamed (Requested renaming column "' . $args->getOldColumnName() . '" to "' . $column->getName() . '" in table "' . $args->getTableDiff()->name . '")');
    }

    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $args)
    {
        $tableColumn = array_change_key_case($args->getTableColumn(), CASE_LOWER);
        $table       = $args->getTable();

        $info = null;

        if ('geometry' === $tableColumn['type']) {
            $info = $this->schemaManager->getGeometrySpatialColumnInfo($table, $tableColumn['field']);
        } elseif ('geography' === $tableColumn['type']) {
            $info = $this->schemaManager->getGeographySpatialColumnInfo($table, $tableColumn['field']);
        }

        if (!$info) {
            return;
        }

        $default = null;

        if (isset($tableColumn['default']) &&
            'NULL::geometry' !== $tableColumn['default'] &&
            'NULL::geography' !== $tableColumn['default']) {
            $default = $tableColumn['default'];
        }

        $options = array(
            'notnull' => (bool) $tableColumn['isnotnull'],
            'default' => $default,
            'comment' => isset($tableColumn['comment']) ? $tableColumn['comment'] : null,
        );

        $column = new Column($tableColumn['field'], PostGISType::getType($tableColumn['type']), $options);

        $column
            ->setCustomSchemaOption('geometry_type', $info['type'])
            ->setCustomSchemaOption('srid', $info['srid'])
        ;

        $args
            ->setColumn($column)
            ->preventDefault();
    }

    public function onSchemaIndexDefinition(SchemaIndexDefinitionEventArgs $args)
    {
        $index = $args->getTableIndex();

        $spatialIndexes = $this->schemaManager->listSpatialIndexes($args->getTable());

        if (!isset($spatialIndexes[$index['name']])) {
            return;
        }

        $spatialIndex = new Index(
            $index['name'],
            $index['columns'],
            $index['unique'],
            $index['primary'],
            array_merge($index['flags'], array('spatial'))
        );

        $args
            ->setIndex($spatialIndex)
            ->preventDefault()
        ;
    }

    public function isSpatialColumnType(Column $column)
    {
        return $column->getType() instanceof PostGISType;
    }
}
