<?php

namespace Dvsa\Olcs\Scanning;

/**
 * Scanning Module
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Module
{
    public function getConfig()
    {
        $base = include __DIR__ . '/../config/module.config.php';
        return $base;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Laminas\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ ,
                ),
            ),
        );
    }
}
