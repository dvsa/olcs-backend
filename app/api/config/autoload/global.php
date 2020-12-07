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
    'olcs-doctrine' => [
        // Default encryption key to use if not overridden in local
        'encryption_key' => 'ASaoW9TQogBu7TgDHoBKtsDPY5BdjF7WFZbLKHgN'
    ],
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                // If running as CLI then use different directory to avoid permissions problems
                'proxy_dir'         => (PHP_SAPI === 'cli') ?
                    sys_get_temp_dir() .'/EntityCli/Proxy' :
                    sys_get_temp_dir() .'/Entity/Proxy',
                'proxy_namespace'   => 'Dvsa\Olcs\Api\Entity\Proxy',
                'datetime_functions' => [
                    'date'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'time'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'timestamp'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'convert_tz'    => 'Oro\ORM\Query\AST\Functions\DateTime\ConvertTz',
                ],
                'numeric_functions' => [
                    'timestampdiff' => 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff',
                    'dayofyear'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'dayofmonth'    => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'dayofweek'     => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'week'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'day'           => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'hour'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'minute'        => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'month'         => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'quarter'       => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'second'        => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'year'          => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'sign'          => 'Oro\ORM\Query\AST\Functions\Numeric\Sign',
                    'pow'           => 'Oro\ORM\Query\AST\Functions\Numeric\Pow',
                ],
                'string_functions'  => [
                    'md5'           => 'Oro\ORM\Query\AST\Functions\SimpleFunction',
                    'group_concat'  => 'Oro\ORM\Query\AST\Functions\String\GroupConcat',
                    'cast'          => 'Oro\ORM\Query\AST\Functions\Cast',
                    'concat_ws'     => 'Oro\ORM\Query\AST\Functions\String\ConcatWs',
                    'replace'       => 'Oro\ORM\Query\AST\Functions\String\Replace',
                    'date_format'   => 'Oro\ORM\Query\AST\Functions\String\DateFormat',
                    'ifnull'        => 'DoctrineExtensions\Query\Mysql\IfNull',
                ]
            ),
        )
    ),
    'document_share' => [
        'http' => [
            'adapter' => Laminas\Http\Client\Adapter\Curl::class,
            'curloptions' => [
                CURLOPT_TIMEOUT => 180,
            ],
        ],
        'path' => 'documents/'
            . '{Category}/{SubCategory}/{Date:Y}/{Date:m}/{Date:YmdHisu}_{Context}_{Description}.{Extension}'
    ],
    'email' => array(
        'http' => array(
            'adapter' => Laminas\Http\Client\Adapter\Curl::class,
        ),
    ),
    'companies_house' => array(
        'http' => array(
            'adapter' => Laminas\Http\Client\Adapter\Curl::class,
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
        'compliance_episode' => [
            'xmlNs' => 'https://webgate.ec.testa.eu/erru/1.0',
        ],
    )
);
