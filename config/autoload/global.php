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
                // If running as CLI then use different directory to avoid permissions problems
                'proxy_dir'         => (PHP_SAPI === 'cli') ?
                    sys_get_temp_dir() .'/EntityCli/Proxy' :
                    sys_get_temp_dir() .'/Entity/Proxy',
                'proxy_namespace'   => 'Dvsa\Olcs\Api\Entity\Proxy',
            ),
        )
    ),
    'document_share' => array(
        'http' => array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl'
        ),
        'path' => 'documents/'
            . '{Category}/{SubCategory}/{Date:Y}/{Date:m}/{Date:YmdHisu}_{Context}_{Description}.{Extension}'
    ),
    'email' => array(
        'http' => array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl'
        ),
    ),
    'companies_house' => array(
        'http' => array(
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
        ),
    ),
    'soflomo_purifier' => array(
        'config' => array(
            'Cache.SerializerPath' => sys_get_temp_dir(),
        ),
    ),
);
