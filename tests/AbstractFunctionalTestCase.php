<?php

declare(strict_types=1);

namespace Jsor\Doctrine\PostGIS;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolEvents;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Jsor\Doctrine\PostGIS\Driver\Middleware;
use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventListener;
use Jsor\Doctrine\PostGIS\Functions\Configurator;
use Jsor\Doctrine\PostGIS\Schema\SchemaManagerFactory;
use Symfony\Bridge\Doctrine\SchemaListener\MessengerTransportDoctrineSchemaListener;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection as MessengerConnection;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\PostgreSqlConnection;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

abstract class AbstractFunctionalTestCase extends AbstractTestCase
{
    private static ?Connection $_conn = null;

    private static ?EventManager $_eventManager = null;

    private static ?MessengerConnection $_messengerConn = null;

    /**
     * Array of entity class name to their tables that were created.
     */
    private static array $_entityTablesCreated = [];

    private ?EntityManagerInterface $_em = null;

    private ?SchemaTool $_schemaTool = null;

    protected function setUp(): void
    {
        parent::setUp();

        static::_registerTypes();
    }

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

    protected function _getEventManager(): EventManager
    {
        if (!self::$_eventManager) {
            self::$_eventManager = new EventManager();
        }

        return self::$_eventManager;
    }

    protected function _getConnection(): Connection
    {
        if (!isset(self::$_conn)) {
            if (class_exists(ORMConfiguration::class)) {
                $config = new ORMConfiguration();
                Configurator::configure($config);
            } else {
                $config = new DBALConfiguration();
            }
            $config->setMiddlewares([new Middleware()]);
            $config->setSchemaManagerFactory(new SchemaManagerFactory());

            self::$_conn = DriverManager::getConnection($this->_getDbParams(), $config, $this->_getEventManager());

            self::$_messengerConn = new PostgreSqlConnection(
                [
                    'table_name' => 'messenger_messages',
                    'auto_setup' => false,
                ],
                self::$_conn,
            );

            $this->_getEventManager()->addEventListener(
                ToolEvents::postGenerateSchema,
                new MessengerTransportDoctrineSchemaListener(
                    [
                        new DoctrineTransport(
                            self::$_messengerConn,
                            new PhpSerializer(),
                        ),
                    ],
                ),
            );

            if (class_exists(ORMConfiguration::class)) {
                $this->_getEventManager()->addEventListener('postGenerateSchemaTable', new ORMSchemaEventListener());
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

    protected function _getMessengerConnection(): MessengerConnection
    {
        self::_getConnection();

        return self::$_messengerConn;
    }

    protected function _getEntityManager(?ORMConfiguration $config = null): EntityManagerInterface
    {
        if (null !== $this->_em) {
            return $this->_em;
        }

        $connection = $this->_getConnection();

        if (!$config) {
            $config = $connection->getConfiguration();
        }

        $this->_setupConfiguration($config);

        $em = new EntityManager($connection, $config, $this->_getEventManager());

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
        $config->setMetadataDriverImpl($this->_getMappingDriver());

        if (PHP_VERSION_ID >= 80400) {
            $config->enableNativeLazyObjects(true);
        } else {
            $config->setProxyDir(__DIR__ . '/tmp');
            $config->setProxyNamespace('Proxy');
        }

        return $config;
    }

    protected function _getMappingDriver(): MappingDriver
    {
        return new AttributeDriver([__DIR__ . '/fixtures/Entity']);
    }

    protected function _execFile($fileName): int
    {
        return $this->_getConnection()->executeStatement(file_get_contents(__DIR__ . '/fixtures/' . $fileName));
    }
}
