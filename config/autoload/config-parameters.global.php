<?php

use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\SecretsManager;
use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\ParameterStore;


return [
    'config_parameters' => [
        'providers' => [
            SecretsManager::class => [
                // Todo: will need to be parameterised once all the terraform is ready.
                'DEVAPPDA-BASE-SM-APPLICATION-SECRETS',
            ],
            ParameterStore::class => [
                '/applicationparams/da/',
            ],
        ],
    ],
];
