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
            TransferQuery\Application\Application::class => QueryHandler\Application\Application::class,
            TransferQuery\Application\FinancialHistory::class => QueryHandler\Application\FinancialHistory::class,
            TransferQuery\Application\FinancialEvidence::class => QueryHandler\Application\FinancialEvidence::class,
            TransferQuery\Application\PreviousConvictions::class => QueryHandler\Application\PreviousConvictions::class,
            TransferQuery\Application\Safety::class => QueryHandler\Application\Safety::class,
            TransferQuery\Application\Declaration::class => QueryHandler\Application\Declaration::class,
            TransferQuery\Application\LicenceHistory::class => QueryHandler\Application\LicenceHistory::class,

            // Licence
            TransferQuery\Licence\BusinessDetails::class => QueryHandler\Licence\BusinessDetails::class,
            TransferQuery\Licence\Licence::class => QueryHandler\Licence\Licence::class,
            TransferQuery\Licence\TypeOfLicence::class => QueryHandler\Licence\TypeOfLicence::class,
            TransferQuery\Licence\Safety::class => QueryHandler\Licence\Safety::class,
            TransferQuery\Licence\Addresses::class => QueryHandler\Licence\Addresses::class,

            // Other Licence
            TransferQuery\OtherLicence\OtherLicence::class => QueryHandler\OtherLicence\OtherLicence::class,

            // Organisation
            TransferQuery\Organisation\BusinessDetails::class => QueryHandler\Organisation\BusinessDetails::class,
            TransferQuery\Organisation\Organisation::class => QueryHandler\Organisation\Organisation::class,

            // Variation
            TransferQuery\Variation\Variation::class => QueryHandler\Variation\Variation::class,
            TransferQuery\Variation\TypeOfLicence::class => QueryHandler\Variation\TypeOfLicence::class,

            // Cases
            TransferQuery\Cases\Pi::class => QueryHandler\Cases\Pi::class,
            TransferQuery\Cases\LegacyOffence::class => QueryHandler\Cases\LegacyOffence::class,
            TransferQuery\Cases\LegacyOffenceList::class => QueryHandler\Cases\LegacyOffenceList::class,
            TransferQuery\Cases\ImpoundingList::class => QueryHandler\Cases\ImpoundingList::class,
            TransferQuery\Cases\Impounding::class => QueryHandler\Cases\Impounding::class,
            TransferQuery\Cases\Complaint\Complaint::class => QueryHandler\Cases\Complaint\Complaint::class,
            TransferQuery\Cases\Complaint\ComplaintList::class => QueryHandler\Cases\Complaint\ComplaintList::class,
            TransferQuery\Cases\EnvironmentalComplaint\EnvironmentalComplaint::class =>
                QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaint::class,
            TransferQuery\Cases\EnvironmentalComplaint\EnvironmentalComplaintList::class =>
                QueryHandler\Cases\EnvironmentalComplaint\EnvironmentalComplaintList::class,

            // Processing
            TransferQuery\Processing\History::class => QueryHandler\Processing\History::class,
            TransferQuery\Processing\Note::class => QueryHandler\Processing\Note::class,
            TransferQuery\Processing\NoteList::class => QueryHandler\Processing\NoteList::class,

            // Conviction - NOT Previous Conviction
            TransferQuery\Cases\Conviction\Conviction::class => QueryHandler\Cases\Conviction\Conviction::class,
            TransferQuery\Cases\Conviction\ConvictionList::class => QueryHandler\Cases\Conviction\ConvictionList::class,

            // Prohibition
            TransferQuery\Cases\Prohibition\Prohibition::class => QueryHandler\Cases\Prohibition\Prohibition::class,
            TransferQuery\Cases\Prohibition\ProhibitionList::class =>
                QueryHandler\Cases\Prohibition\ProhibitionList::class,

            // Prohibition / Defect
            TransferQuery\Cases\Prohibition\Defect::class => QueryHandler\Cases\Prohibition\Defect::class,
            TransferQuery\Cases\Prohibition\DefectList::class => QueryHandler\Cases\Prohibition\DefectList::class,

            // Previous Conviction
            TransferQuery\PreviousConviction\PreviousConviction::class =>
                QueryHandler\PreviousConviction\PreviousConviction::class,

            // Company Subsidiary
            TransferQuery\CompanySubsidiary\CompanySubsidiary::class
            => QueryHandler\CompanySubsidiary\CompanySubsidiary::class,

            // Bus
            TransferQuery\Bus\BusReg::class => QueryHandler\Bus\Bus::class,

            // Trailer
            TransferQuery\Trailer\Trailers::class => QueryHandler\Trailer\Trailers::class,
            TransferQuery\Trailer\Trailers::class => QueryHandler\Trailer\Trailers::class,

            // Grace Periods
            TransferQuery\GracePeriod\GracePeriod::class => QueryHandler\GracePeriod\GracePeriod::class,
            TransferQuery\GracePeriod\GracePeriods::class => QueryHandler\GracePeriod\GracePeriods::class,

            // Irfo
            TransferQuery\Irfo\IrfoDetails::class => QueryHandler\Irfo\IrfoDetails::class,
            TransferQuery\Irfo\IrfoGvPermit::class => QueryHandler\Irfo\IrfoGvPermit::class,
            TransferQuery\Irfo\IrfoGvPermitList::class => QueryHandler\Irfo\IrfoGvPermitList::class,
            TransferQuery\Irfo\IrfoPermitStockList::class => QueryHandler\Irfo\IrfoPermitStockList::class,
            TransferQuery\Irfo\IrfoPsvAuth::class => QueryHandler\Irfo\IrfoPsvAuth::class,
            TransferQuery\Irfo\IrfoPsvAuthList::class => QueryHandler\Irfo\IrfoPsvAuthList::class,

            // Workshop
            TransferQuery\Workshop\Workshop::class => QueryHandler\Workshop\Workshop::class,

            // Correspondence
            TransferQuery\Correspondence\Correspondence::class => QueryHandler\Correspondence\Correspondence::class,
            TransferQuery\Correspondence\Correspondences::class => QueryHandler\Correspondence\Correspondences::class,

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
            'Conviction' => RepositoryFactory::class,
            'Organisation' => RepositoryFactory::class,
            'Licence' => RepositoryFactory::class,
            'Bus' => RepositoryFactory::class,
            'BusRegOtherService' => RepositoryFactory::class,
            'BusNoticePeriod' => RepositoryFactory::class,
            'Trailer' => RepositoryFactory::class,
            'GracePeriod' => RepositoryFactory::class,
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
            'Prohibition' => RepositoryFactory::class,
            'ProhibitionDefect' => RepositoryFactory::class,
            'LegacyOffence' => RepositoryFactory::class,
            'LegacyOffenceList' => RepositoryFactory::class,
            'Note' => RepositoryFactory::class,
            'TradingName' => RepositoryFactory::class,
            'IrfoGvPermit' => RepositoryFactory::class,
            'IrfoPermitStock' => RepositoryFactory::class,
            'IrfoPsvAuth' => RepositoryFactory::class,
            'IrfoPsvAuthNumber' => RepositoryFactory::class,
            'Impounding' => RepositoryFactory::class,
            'ImpoundingList' => RepositoryFactory::class,
            'Workshop' => RepositoryFactory::class,
            'FinancialStandingRate' => RepositoryFactory::class,
            'Complaint' => RepositoryFactory::class,
            'PhoneContact' => RepositoryFactory::class,
            'OtherLicence' => RepositoryFactory::class,
            'Correspondence' => RepositoryFactory::class,
            'SystemParameter' => RepositoryFactory::class,
            'TaskAllocationRule' => RepositoryFactory::class,
            'IrfoPartner' => RepositoryFactory::class,
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
