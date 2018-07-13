<?php

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Jsor\Doctrine\PostGIS\\', __DIR__ . '/fixtures');
$loader->addPsr4('Jsor\Doctrine\PostGIS\\', __DIR__);

$GLOBALS['TESTS_TEMP_DIR'] = __DIR__ . '/temp';

Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
