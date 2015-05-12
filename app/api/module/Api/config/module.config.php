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
    ],
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
                    'yesno' => 'Olcs\Db\Entity\Types\YesNoType',
                    'yesnonull' => 'Olcs\Db\Entity\Types\YesNoNullType',
                    'date' => 'Olcs\Db\Entity\Types\DateType',
                    'datetime' => 'Olcs\Db\Entity\Types\DateTimeType',
                ]
            ]
        ]
    ]
];
