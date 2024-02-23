<?php

use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\SecretsManager;
use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\ParameterStore;

$environment = getenv('ENVIRONMENT_NAME');

$providers = [];

if (!empty($environment)) {
    $providers = [
        SecretsManager::class => [
            sprintf('DEVAPP%s-BASE-SM-APPLICATION-API', strtoupper($environment)),
        ],
        ParameterStore::class => [
            sprintf('/applicationparams/%s/', strtolower($environment)),
        ],
    ];
}

return [
    'aws' => [
        'global' => [
            'http'    => [
                'connect_timeout' => 5,
                'timeout'         => 5,
            ],
        ],
    ],
    'config_parameters' => [
        'providers' => $providers,
    ],
];
