<?php

namespace Dvsa\Olcs\GdsVerify;

/**
 * GDS Verify Module
 */
class Module
{
    /**
     * Get module config
     *
     * @return array
     */
    public function getConfig()
    {
        $base = include __DIR__ . '/../config/module.config.php';
        return $base;
    }

    /**
     * Get Autoloader config
     *
     * @return array
     */
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
