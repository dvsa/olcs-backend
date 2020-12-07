<?php

if (file_exists('vendor/olcs/autoload/autoload.php')) {
    $loader = include 'vendor/olcs/autoload/autoload.php';
} elseif (file_exists('vendor/autoload.php')) {
    // Composer autoloading
    $loader = include 'vendor/autoload.php';
}

if (!class_exists('Laminas\Loader\AutoloaderFactory')) {
    throw new RuntimeException(
        'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.'
    );
}
