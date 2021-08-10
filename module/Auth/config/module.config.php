<?php

return [
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\Auth\Service\AuthenticationServiceInterface::class => \Dvsa\Olcs\Auth\Service\AuthenticationServiceFactory::class,
            \Laminas\Authentication\Adapter\ValidatableAdapterInterface::class => \Dvsa\Olcs\Auth\Adapter\ValidatableAdapterFactory::class,
            \Dvsa\Olcs\Auth\Adapter\OpenAm::class => \Dvsa\Olcs\Auth\Adapter\OpenAmFactory::class,
            \Dvsa\Olcs\Auth\Client\OpenAm::class => \Dvsa\Olcs\Auth\Client\OpenAmFactory::class,
            \Dvsa\Olcs\Auth\Client\UriBuilder::class => \Dvsa\Olcs\Auth\Client\UriBuilderFactory::class,
            \Dvsa\Authentication\Cognito\Client::class => \Dvsa\Olcs\Auth\Client\CognitoClientFactory::class,
            \Dvsa\Olcs\Auth\Adapter\CognitoAdapter::class => \Dvsa\Olcs\Auth\Adapter\CognitoAdapterFactory::class
        ],
    ],
];
