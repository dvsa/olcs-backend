<?php

require_once __DIR__ . '/../vendor/composer/ClassLoader.php';
require_once __DIR__ . '/CustomClassLoader.php';
require_once __DIR__ . '/CustomAutoloader.php';

return CustomAutoloader::getLoader();
