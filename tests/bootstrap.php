<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$GLOBALS['TESTS_TEMP_DIR'] = __DIR__ . '/temp';

if (class_exists(Doctrine\Common\Annotations\AnnotationRegistry::class)) {
    Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
}
