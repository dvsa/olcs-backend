<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'proxy_dir'         => sys_get_temp_dir() . '/OlcsBe/Proxy',
                'proxy_namespace'   => 'OlcsBe\Proxy',
            ),
        )
    ),
    'jackrabbit' => array(
        'http' => array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl'
        ),
    )
);
