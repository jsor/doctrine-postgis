<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Jsor\Doctrine\PostGIS\Event\ORMSchemaEventSubscriber;

$loader = require __DIR__ . '/../vendor/autoload.php';

$GLOBALS['TESTS_TEMP_DIR'] = __DIR__ . '/temp';

//$connection = DriverManager::getConnection([
//    'driver' => $GLOBALS['db_type'],
//    'user' => $GLOBALS['db_username'],
//    'password' => $GLOBALS['db_password'],
//    'host' => $GLOBALS['db_host'],
//    'dbname' => $GLOBALS['db_name'],
//    'port' => $GLOBALS['db_port'],
//], new Configuration());
//$connection->getEventManager()->addEventSubscriber(new ORMSchemaEventSubscriber());
//$connection->executeQuery('CREATE EXTENSION IF NOT EXISTS postgis');
