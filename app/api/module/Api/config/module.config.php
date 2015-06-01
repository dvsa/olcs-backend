<?php

use Dvsa\Olcs\Transfer\Query as TransferQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryFactory;
use Dvsa\Olcs\Api\Domain\QueryPartial;
use Dvsa\Olcs\Api\Domain\Util;

return [
    'router' => [
        'routes' => include(__DIR__ . '/../../../vendor/olcs/olcs-transfer/config/backend-routes.config.php')
    ],
    'service_manager' => [
        'factories' => [
            'IdentityProvider' => \Dvsa\Olcs\Api\Rbac\IdentityProvider::class,
            'PayloadValidationListener' => \Dvsa\Olcs\Api\Mvc\PayloadValidationListenerFactory::class,
            'CommandHandlerManager' => \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::class,
            'QueryHandlerManager' => \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::class,
            'QueryPartialServiceManager' => \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::class,
            'RepositoryServiceManager' => \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::class,
            'QueryBuilder' => \Dvsa\Olcs\Api\Domain\QueryBuilderFactory::class,
            Util\SlaCalculatorInterface::class => Util\SlaCalculatorFactory::class,
            Util\TimeProcessorBuilderInterface::class => Util\TimeProcessorBuilderFactory::class
        ]
    ],
    'controller_plugins' => [
        'invokables' => [
            'response' => \Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response::class,
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Api\Generic' => \Dvsa\Olcs\Api\Controller\GenericController::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            // Transfer - Application
            \Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateTypeOfLicence::class,
            \Dvsa\Olcs\Transfer\Command\Application\CreateApplication::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateApplication::class,
            // Transfer - Licence
            \Dvsa\Olcs\Transfer\Command\Licence\UpdateTypeOfLicence::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateTypeOfLicence::class,
            // Transfer - Organisation
            \Dvsa\Olcs\Transfer\Command\Organisation\UpdateBusinessType::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\UpdateBusinessType::class,
            // Domain - Application
            \Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateApplicationFee::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\ResetApplication::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\GenerateLicenceNumber::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\GenerateLicenceNumber::class,
            \Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateApplicationCompletion::class,
            // Domain - Licence
            \Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CancelLicenceFees::class,
            // Domain - Task
            \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Task\CreateTask::class,
            // Domain - Fee
            \Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\Fee\CreateFee::class,
            // Domain - ApplicationCompletion
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTypeOfLicenceStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateTypeOfLicenceStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateAddressesStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateAddressesStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateBusinessTypeStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateBusinessTypeStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateConvictionsPenaltiesStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateConvictionsPenaltiesStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateFinancialEvidenceStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateFinancialEvidenceStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateFinancialHistoryStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateFinancialHistoryStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateLicenceHistoryStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateLicenceHistoryStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateOperatingCentresStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateOperatingCentresStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdatePeopleStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdatePeopleStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateSafetyStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateSafetyStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateVehiclesStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateVehiclesStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateUndertakingsStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateUndertakingsStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateConditionsUndertakingsStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateConditionsUndertakingsStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateVehiclesDeclarationsStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateVehiclesDeclarationsStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateVehiclesPsvStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateVehiclesPsvStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTransportManagersStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateTransportManagersStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTaxiPhvStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateTaxiPhvStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateCommunityLicencesStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateCommunityLicencesStatus::class,
            \Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateBusinessDetailsStatus::class
                => \Dvsa\Olcs\Api\Domain\CommandHandler\ApplicationCompletion\UpdateBusinessDetailsStatus::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            TransferQuery\Application\Application::class => QueryHandler\Application\Application::class,
            TransferQuery\Licence\Licence::class => QueryHandler\Licence\Licence::class,
            TransferQuery\Licence\TypeOfLicence::class => QueryHandler\Licence\TypeOfLicence::class,
            TransferQuery\Variation\Variation::class => QueryHandler\Variation\Variation::class,
            TransferQuery\Variation\TypeOfLicence::class => QueryHandler\Variation\TypeOfLicence::class,
            TransferQuery\Organisation\Organisation::class => QueryHandler\Organisation\Organisation::class,
            TransferQuery\Cases\Pi::class => QueryHandler\Cases\Pi::class,
            TransferQuery\Application\FinancialHistory::class => QueryHandler\Application\FinancialHistory::class,
            TransferQuery\Processing\History::class => QueryHandler\Processing\History::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'withRefdata' => QueryPartial\WithRefdataFactory::class,
            'withUser' => QueryPartial\WithUserFactory::class,
        ],
        'invokables' => [
            'byId' => QueryPartial\ById::class,
            'with' => QueryPartial\With::class,
            'paginate' => QueryPartial\Paginate::class,
            'order' => QueryPartial\Order::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'Application' => RepositoryFactory::class,
            'Organisation' => RepositoryFactory::class,
            'Licence' => RepositoryFactory::class,
            'Task' => RepositoryFactory::class,
            'FeeType' => RepositoryFactory::class,
            'Fee' => RepositoryFactory::class,
            'Cases' => RepositoryFactory::class,
            'Pi' => \Dvsa\Olcs\Api\Domain\Repository\PiFactory::class,
            'EventHistory' => RepositoryFactory::class,
            'PublicHoliday' => RepositoryFactory::class,
            'Sla' => RepositoryFactory::class,
            'LicenceNoGen' => RepositoryFactory::class,
            'User' => RepositoryFactory::class,
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
    ],
    'zfc_rbac' => [
        'identity_provider' => 'IdentityProvider',
        'role_provider' => [
            'ZfcRbac\Role\ObjectRepositoryRoleProvider' => [
                'object_manager'     => 'doctrine.entitymanager.orm_default',
                'class_name'         => \Dvsa\Olcs\Api\Entity\User\Role::class,
                'role_name_property' => 'role'
            ]
        ],
        'assertion_map' => [
            'can-update-licence-licence-type' => \Dvsa\Olcs\Api\Assertion\Licence\UpdateLicenceType::class
        ]
    ],
];
