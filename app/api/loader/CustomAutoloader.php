<?php

/**
 * Unfortunately Composer doesn't make it easy to extend it's autoloader
 *  so here we duplicate a bit of their code
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CustomAutoloader
{
    private static $loader;

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        self::$loader = $loader = new \Autoload\CustomClassLoader();

        $includePaths = require __DIR__ . '/../vendor/composer/include_paths.php';
        array_push($includePaths, get_include_path());
        set_include_path(join(PATH_SEPARATOR, $includePaths));

        $map = require __DIR__ . '/../vendor/composer/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->set($namespace, $path);
        }

        $map = require __DIR__ . '/../vendor/composer/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            $loader->setPsr4($namespace, $path);
        }

        $dynamicClassMap = require __DIR__ . '/classmap.php';
        if ($dynamicClassMap) {
            $loader->addDynamicClassMap($dynamicClassMap);
        }

        $classMap = require __DIR__ . '/../vendor/composer/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }

        $loader->register(true);

        return $loader;
    }
}
