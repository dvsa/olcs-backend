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
    'document_share' => [
        'http' => [
            'adapter' => Zend\Http\Client\Adapter\Curl::class,
            'curloptions' => [
                CURLOPT_TIMEOUT => 180,
            ],
        ],
        'path' => 'documents/'
            . '{Category}/{SubCategory}/{Date:Y}/{Date:m}/{Date:YmdHisu}_{Context}_{Description}.{Extension}'
    ],
    'email' => array(
        'http' => array(
            'adapter' => Zend\Http\Client\Adapter\Curl::class,
        ),
    ),
    'companies_house' => array(
        'http' => array(
            'adapter' => Zend\Http\Client\Adapter\Curl::class,
        ),
    ),
    'soflomo_purifier' => array(
        'config' => array(
            'Cache.SerializerPath' => sys_get_temp_dir(),
        ),
    ),
    'ebsr' => array(
        'max_schema_errors' => 3, //maximum number of xml schema problems to return (prevents massive error messages)
        'transxchange_schema_version' => 2.5 //validate against transxchange schema (2.1, 2.4 and 2.5 available)
    ),
    'nr' => array(
        'max_schema_errors' => 10, //maximum number of xml schema problems to return (prevents massive error messages)
    )
);
