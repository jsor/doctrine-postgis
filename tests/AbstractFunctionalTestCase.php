<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Jsor\Doctrine\PostGIS\Event\DBALSchemaEventSubscriber;
use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventSubscriber;
use Jsor\Doctrine\PostGIS\Functions\Configurator;

abstract class AbstractFunctionalTestCase extends AbstractTestCase
{
    private static ?Connection $_conn = null;

    /**
     * Array of entity class name to their tables that were created.
     */
    private static array $_entityTablesCreated = [];

    private ?EntityManagerInterface $_em = null;

    private ?SchemaTool $_schemaTool = null;

    protected function tearDown(): void
    {
        parent::tearDown();

        if (!self::$_entityTablesCreated) {
            return;
        }

        $em = $this->_getEntityManager();

        $classes = [];
        foreach (self::$_entityTablesCreated as $className => $flag) {
            $classes[] = $em->getClassMetadata($className);
        }

        $this->_getSchemaTool()->dropSchema($classes);

        self::$_entityTablesCreated = [];
    }

    protected function _setUpEntitySchema($classNames): void
    {
        $em = $this->_getEntityManager();

        $classes = [];
        foreach ((array) $classNames as $className) {
            if (!isset(self::$_entityTablesCreated[$className])) {
                self::$_entityTablesCreated[$className] = true;
                $classes[] = $em->getClassMetadata($className);
            }
        }

        if ($classes) {
            $this->_getSchemaTool()->dropSchema($classes);
            $this->_getSchemaTool()->createSchema($classes);
        }
    }

    protected function _getDbParams(): array
    {
        return [
            'driver' => getenv('DB_TYPE'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
        ];
    }

    protected function _getConnection(): Connection
    {
        if (!isset(self::$_conn)) {
            if (class_exists(ORMConfiguration::class)) {
                self::$_conn = DriverManager::getConnection($this->_getDbParams(), new ORMConfiguration());

                self::$_conn->getEventManager()->addEventSubscriber(new ORMSchemaEventSubscriber());

                Configurator::configure(self::$_conn->getConfiguration());
            } else {
                self::$_conn = DriverManager::getConnection($this->_getDbParams(), new DBALConfiguration());

                self::$_conn->getEventManager()->addEventSubscriber(new DBALSchemaEventSubscriber());
            }

            if (!Type::hasType('tsvector')) {
                Type::addType('tsvector', 'Doctrine\DBAL\Types\TextType');
            }

            $platform = self::$_conn->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('tsvector', 'tsvector');

            // Prevent "Unknown database type..." exceptions thrown during
            // database inspections by the schema tool
            $platform->registerDoctrineTypeMapping('_text', 'string');
        }

        return self::$_conn;
    }

    protected function _getEntityManager(ORMConfiguration $config = null): EntityManager
    {
        if (null !== $this->_em) {
            return $this->_em;
        }

        $connection = $this->_getConnection();

        if (!$config) {
            $config = $connection->getConfiguration();
        }

        $this->_setupConfiguration($config);

        $em = EntityManager::create($connection, $config);

        return $this->_em = $em;
    }

    protected function _getSchemaTool(): SchemaTool
    {
        if (null !== $this->_schemaTool) {
            return $this->_schemaTool;
        }

        return $this->_schemaTool = new SchemaTool($this->_getEntityManager());
    }

    protected function _setupConfiguration(ORMConfiguration $config): ORMConfiguration
    {
        $config->setProxyDir($GLOBALS['TESTS_TEMP_DIR']);
        $config->setProxyNamespace('Proxy');
        $config->setMetadataDriverImpl($this->_getMappingDriver());

        return $config;
    }

    protected function _getMappingDriver(): MappingDriver
    {
        $reader = new AnnotationReader();

        return new AnnotationDriver($reader);
    }

    protected function _execFile($fileName): int
    {
        return $this->_getConnection()->exec(file_get_contents(__DIR__ . '/fixtures/' . $fileName));
    }
}
