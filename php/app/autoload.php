<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

require_once __DIR__.'/bootstrap.php.cache';

$loader = require __DIR__.'/../vendor/autoload.php';

require __DIR__ . '/environment.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
