<?php

return [
    'router' => [
        'routes' => include(__DIR__ . '/../../../vendor/olcs/olcs-transfer/config/backend-routes.config.php')
    ],
    'service_manager' => [
        'factories' => [
            'PayloadValidationListener' => \Dvsa\Olcs\Api\Mvc\PayloadValidationListenerFactory::class,
            'DomainServiceManager' => \Dvsa\Olcs\Api\Domain\DomainServiceManagerFactory::class,
            'QueryPartialServiceManager' => \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::class,
            'RepositoryServiceManager' => \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::class,
            'QueryBuilder' => \Dvsa\Olcs\Api\Domain\QueryBuilderFactory::class,
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Api\Application' => \Dvsa\Olcs\Api\Controller\ApplicationController::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\DomainServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'Application' => \Dvsa\Olcs\Api\Domain\Service\ServiceFactory::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'withRefdata' => \Dvsa\Olcs\Api\Domain\QueryPartial\WithRefdataFactory::class,
        ],
        'invokables' => [
            'byId' => \Dvsa\Olcs\Api\Domain\QueryPartial\ById::class
        ]
    ],
    \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'Application' => \Dvsa\Olcs\Api\Domain\Repository\RepositoryFactory::class,
            'Licence' => \Dvsa\Olcs\Api\Domain\Repository\RepositoryFactory::class
        ]
    ]
];
