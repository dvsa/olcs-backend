<?php

return [
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\Auth\Service\AuthenticationServiceInterface::class => \Dvsa\Olcs\Auth\Service\AuthenticationServiceFactory::class,
            \Laminas\Authentication\Adapter\ValidatableAdapterInterface::class => \Dvsa\Olcs\Auth\Adapter\ValidatableAdapterFactory::class,
        ],
    ],
];
