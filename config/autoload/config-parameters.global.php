<?php

use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\SecretsManager;
use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\ParameterStore;

$environment = $_ENV['ENVIRONMENT_NAME'] ?? null;

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
    'config_parameters' => [
        'providers' => $providers,
    ],
];
