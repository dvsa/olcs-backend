<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * This autoloading setup is really more complicated than it needs to be for most
 * applications. The added complexity is simply to reduce the time it takes for
 * new developers to be productive with a fresh skeleton. It allows autoloading
 * to be correctly configured, regardless of the installation method and keeps
 * the use of composer completely optional. This setup should work fine for
 * most users, however, feel free to configure autoloading however you'd like.
 */

if (file_exists('loader/autoload.php')) {
    $loader = include 'loader/autoload.php';
} elseif (file_exists('vendor/autoload.php')) {
    // Composer autoloading
    $loader = include 'vendor/autoload.php';
}

$zf2Path = false;

if (is_dir('vendor/ZF2/library')) {
    $zf2Path = 'vendor/ZF2/library';
} elseif (getenv('ZF2_PATH')) {
    $zf2Path = getenv('ZF2_PATH');
} elseif (get_cfg_var('zf2_path')) {
    $zf2Path = get_cfg_var('zf2_path');
}

if ($zf2Path) {
    if (isset($loader)) {
        $loader->add('Zend', $zf2Path);
    } else {
        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        Zend\Loader\AutoloaderFactory::factory(
            array(
                'Zend\Loader\StandardAutoloader' => array(
                    'autoregister_zf' => true
                )
            )
        );
    }
}

if (!class_exists('Zend\Loader\AutoloaderFactory')) {
    throw new RuntimeException(
        'Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.'
    );
}
