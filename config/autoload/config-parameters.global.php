<?php

use Dvsa\LaminasConfigCloudParameters\ParameterProvider\Aws\SecretsManager;

return [
    'config_parameters' => [
        'providers' => [
            SecretsManager::class => [
                // Todo: will need to be parameterised once all the terraform is ready.
                'DEVAPPDA-BASE-SM-APPLICATION-SECRETS',
            ],
        ],
    ],
];
