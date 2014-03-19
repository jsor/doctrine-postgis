<?php

namespace Jsor\Doctrine\PostGIS;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use Doctrine\ORM\Tools\SchemaTool;
use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventSubscriber;

abstract class AbstractFunctionalTestCase extends AbstractTestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private static $_conn;

    /**
     * Array of entity class name to their tables that were created.
     *
     * @var array
     */
    private static $_entityTablesCreated = array();

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $_schemaTool;

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    protected function _setUpEntitySchema($classNames)
    {
        $classes = array();
        foreach ((array) $classNames as $className) {
            if (!isset(static::$_entityTablesCreated[$className])) {
                static::$_entityTablesCreated[$className] = true;
                $classes[] = $this->_em->getClassMetadata($className);
            }
        }

        if ($classes) {
            $this->_getSchemaTool()->dropSchema($classes);
            $this->_getSchemaTool()->createSchema($classes);
        }
    }

    protected function _getConnection()
    {
        if (!isset(self::$_conn)) {
            $dbParams = array(
                'driver' => $GLOBALS['db_type'],
                'user' => $GLOBALS['db_username'],
                'password' => $GLOBALS['db_password'],
                'host' => $GLOBALS['db_host'],
                'dbname' => $GLOBALS['db_name'],
                'port' => $GLOBALS['db_port']
            );

            self::$_conn = DriverManager::getConnection($dbParams);

            self::$_conn->getEventManager()->addEventSubscriber(new ORMSchemaEventSubscriber());

            if (!Type::hasType('tsvector')) {
                Type::addType('tsvector', 'Doctrine\DBAL\Types\TextType');
            }

            $platform = self::$_conn->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('tsvector', 'tsvector');
        }

        return self::$_conn;
    }

    protected function _getEntityManager(Configuration $config = null)
    {
        if (null !== $this->_em) {
            return $this->_em;
        }

        $config = is_null($config) ? $this->_getConfiguration() : $config;
        $em = EntityManager::create($this->_getConnection(), $config);

        return $this->_em = $em;
    }

    protected function _getSchemaTool()
    {
        if (null !== $this->_schemaTool) {
            return $this->_schemaTool;
        }

        return $this->_schemaTool = new SchemaTool($this->_getEntityManager());
    }

    protected function _getConfiguration()
    {
        // We need to mock every method except the ones which
        // handle the filters
        $configurationClass = 'Doctrine\ORM\Configuration';
        $refl = new \ReflectionClass($configurationClass);
        $methods = $refl->getMethods();

        $mockMethods = array();

        foreach ($methods as $method) {
            if (!in_array($method->name, ['addFilter', 'getFilterClassName', 'addCustomNumericFunction', 'getCustomNumericFunction'])) {
                $mockMethods[] = $method->name;
            }
        }

        $config = $this->getMock($configurationClass, $mockMethods);

        $config
            ->expects($this->once())
            ->method('getProxyDir')
            ->will($this->returnValue($GLOBALS['TESTS_TEMP_DIR']))
        ;

        $config
            ->expects($this->once())
            ->method('getProxyNamespace')
            ->will($this->returnValue('Proxy'))
        ;

        $config
            ->expects($this->once())
            ->method('getAutoGenerateProxyClasses')
            ->will($this->returnValue(true))
        ;

        $config
            ->expects($this->once())
            ->method('getClassMetadataFactoryName')
            ->will($this->returnValue('Doctrine\\ORM\\Mapping\\ClassMetadataFactory'))
        ;

        $mappingDriver = $this->_getMappingDriver();

        $config
            ->expects($this->any())
            ->method('getMetadataDriverImpl')
            ->will($this->returnValue($mappingDriver))
        ;

        $config
            ->expects($this->any())
            ->method('getDefaultRepositoryClassName')
            ->will($this->returnValue('Doctrine\\ORM\\EntityRepository'))
        ;

        $config
            ->expects($this->any())
            ->method('getQuoteStrategy')
            ->will($this->returnValue(new DefaultQuoteStrategy()))
        ;

        $config
            ->expects($this->any())
            ->method('getRepositoryFactory')
            ->will($this->returnValue(new DefaultRepositoryFactory()))
        ;

        return $config;
    }

    /**
     * Creates default mapping driver
     *
     * @return \Doctrine\ORM\Mapping\Driver\Driver
     */
    protected function _getMappingDriver()
    {
        $reader = new AnnotationReader();
        $reader = new CachedReader($reader, new ArrayCache());

        return new AnnotationDriver($reader);
    }

    protected function _execFile($fileName)
    {
        return $this->_getConnection()->exec(file_get_contents(__DIR__.'/fixtures/' . $fileName));
    }
}
