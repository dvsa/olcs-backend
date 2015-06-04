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
            Util\TimeProcessorBuilderInterface::class => Util\TimeProcessorBuilderFactory::class,
            'TransactionManager' => \Dvsa\Olcs\Api\Domain\Repository\TransactionManagerFactory::class,
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
        'factories' => require(__DIR__ . '/command-map.config.php')
    ],
    \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            // Application
            TransferQuery\Application\FinancialHistory::class
                => QueryHandler\Application\FinancialHistory::class,

            // Licence
            TransferQuery\Licence\BusinessDetails::class
                => QueryHandler\Licence\BusinessDetails::class,

            // Organisation
            TransferQuery\Organisation\BusinessDetails::class
                => QueryHandler\Organisation\BusinessDetails::class,

            TransferQuery\Application\Application::class => QueryHandler\Application\Application::class,
            TransferQuery\Licence\Licence::class => QueryHandler\Licence\Licence::class,
            TransferQuery\Licence\TypeOfLicence::class => QueryHandler\Licence\TypeOfLicence::class,
            TransferQuery\Variation\Variation::class => QueryHandler\Variation\Variation::class,
            TransferQuery\Variation\TypeOfLicence::class => QueryHandler\Variation\TypeOfLicence::class,
            TransferQuery\Organisation\Organisation::class => QueryHandler\Organisation\Organisation::class,
            TransferQuery\Cases\Pi::class => QueryHandler\Cases\Pi::class,
            TransferQuery\Application\FinancialHistory::class => QueryHandler\Application\FinancialHistory::class,
            TransferQuery\Application\FinancialEvidence::class => QueryHandler\Application\FinancialEvidence::class,
            TransferQuery\Processing\History::class => QueryHandler\Processing\History::class,
            TransferQuery\Application\PreviousConvictions::class =>
                QueryHandler\Application\PreviousConvictions::class,
            TransferQuery\PreviousConviction\PreviousConviction::class =>
                QueryHandler\PreviousConviction\PreviousConviction::class,
            TransferQuery\Cases\LegacyOffence::class => QueryHandler\Cases\LegacyOffence::class,
            TransferQuery\Cases\LegacyOffenceList::class => QueryHandler\Cases\LegacyOffenceList::class,
            TransferQuery\Application\Declaration::class => QueryHandler\Application\Declaration::class,
            TransferQuery\Application\LicenceHistory::class => QueryHandler\Application\LicenceHistory::class,
            TransferQuery\OtherLicence\OtherLicence::class => QueryHandler\OtherLicence\OtherLicence::class,
            TransferQuery\Processing\Note::class => QueryHandler\Processing\Note::class,
            TransferQuery\Processing\NoteList::class => QueryHandler\Processing\NoteList::class,

            TransferQuery\CompanySubsidiary\CompanySubsidiary::class
                => QueryHandler\CompanySubsidiary\CompanySubsidiary::class,

            TransferQuery\Bus\BusReg::class => QueryHandler\Bus\Bus::class,
            TransferQuery\Trailer\Trailers::class => QueryHandler\Trailer\Trailers::class,
            TransferQuery\Irfo\IrfoGvPermit::class => QueryHandler\Irfo\IrfoGvPermit::class,
            TransferQuery\Irfo\IrfoGvPermitList::class => QueryHandler\Irfo\IrfoGvPermitList::class,
            TransferQuery\Cases\ImpoundingList::class => QueryHandler\Cases\ImpoundingList::class,
            TransferQuery\Cases\Impounding::class => QueryHandler\Cases\Impounding::class,
            TransferQuery\Cases\Complaint\Complaint::class => QueryHandler\Cases\Complaint\Complaint::class,
            TransferQuery\Cases\Complaint\ComplaintList::class =>
                QueryHandler\Cases\Complaint\ComplaintList::class,
            TransferQuery\Application\LicenceHistory::class
                => QueryHandler\Application\LicenceHistory::class,
            TransferQuery\OtherLicence\OtherLicence::class
                => QueryHandler\OtherLicence\OtherLicence::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'withContactDetails' => QueryPartial\WithContactDetailsFactory::class,
            'withCreatedBy'      => QueryPartial\WithCreatedByFactory::class,
            'withRefdata' => QueryPartial\WithRefdataFactory::class,
            'withUser' => QueryPartial\WithUserFactory::class,
            'WithPersonContactDetails' => QueryPartial\WithPersonContactDetailsFactory::class,
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
            'Address' => RepositoryFactory::class,
            'ContactDetails' => RepositoryFactory::class,
            'CompanySubsidiary' => RepositoryFactory::class,
            'Organisation' => RepositoryFactory::class,
            'Licence' => RepositoryFactory::class,
            'Bus' => RepositoryFactory::class,
            'Trailer' => RepositoryFactory::class,
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
            'PreviousConviction' => RepositoryFactory::class,
            'LegacyOffence' => RepositoryFactory::class,
            'LegacyOffenceList' => RepositoryFactory::class,
            'Note' => RepositoryFactory::class,
            'TradingName' => RepositoryFactory::class,
            'IrfoGvPermit' => RepositoryFactory::class,
            'Impounding' => RepositoryFactory::class,
            'FinancialStandingRate' => RepositoryFactory::class,
            'Complaint' => RepositoryFactory::class,
            'OtherLicence' => RepositoryFactory::class,
            'IrfoGvPermit' => RepositoryFactory::class,
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
