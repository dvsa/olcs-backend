<?php

return [
    'router' => [
        'routes' => include(__DIR__ . '/../../../vendor/olcs/olcs-transfer/config/backend-routes.config.php')
    ],
    'service_manager' => [
        'factories' => [
            'PayloadValidationListener' => \Dvsa\Olcs\Api\Mvc\PayloadValidationListenerFactory::class,
            'CommandHandlerManager' => \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::class,
            'QueryHandlerManager' => \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::class,
            'QueryPartialServiceManager' => \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::class,
            'RepositoryServiceManager' => \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::class,
            'QueryBuilder' => \Dvsa\Olcs\Api\Domain\QueryBuilderFactory::class,
        ]
    ],
    'controller_plugins' => [
        'invokables' => [
            'response' => \Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response::class,
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Api\Application' => \Dvsa\Olcs\Api\Controller\Application\ApplicationController::class,
            'Api\Application\TypeOfLicence' => \Dvsa\Olcs\Api\Controller\Application\TypeOfLicenceController::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            // Transfer - Application
            \Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateTypeOfLicence::class,
            // Domain - Application
            \Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateApplicationFee::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\ResetApplication::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\GenerateLicenceNumber::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\GenerateLicenceNumber::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateApplicationCompletion::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\ResetCompletionStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\ResetCompletionStatus::class,
            // Domain - Licence
            \Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CancelLicenceFees::class,
            // Domain - Task
            \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Task\CreateTask::class,
            // Domain - Fee
            \Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Fee\CreateFee::class
        ]
    ],
    \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            \Dvsa\Olcs\Transfer\Query\Application\Application::class
                => \Dvsa\Olcs\Api\Domain\QueryHandler\Application\Application::class,
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
    ],
    'entity_namespaces' => include(__DIR__ . '/namespace.config.php'),
    'doctrine' => [
        'driver' => [
            'EntityDriver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ]
            ],
            'translatable_metadata_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    'vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Dvsa\Olcs\Api\Entity' => 'EntityDriver',
                    'Gedmo\Translatable\Entity' => 'translatable_metadata_driver'
                ]
            ]
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    'Gedmo\SoftDeleteable\SoftDeleteableListener',
                    'Gedmo\Translatable\TranslatableListener'
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'filters' => [
                    'soft-deleteable' => 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter'
                ],
                'types' => [
                    'yesno' => 'Dvsa\Olcs\Api\Entity\Types\YesNoType',
                    'yesnonull' => 'Dvsa\Olcs\Api\Entity\Types\YesNoNullType',
                    'date' => 'Dvsa\Olcs\Api\Entity\Types\DateType',
                    'datetime' => 'Dvsa\Olcs\Api\Entity\Types\DateTimeType',
                ]
            ]
        ]
    ]
];
