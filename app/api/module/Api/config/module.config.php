<?php

use Dvsa\Olcs\Api\Domain\Repository\RepositoryFactory;
use Dvsa\Olcs\Api\Domain\QueryPartial;
use Dvsa\Olcs\Api\Domain\Util;
use Dvsa\Olcs\Api\Domain\Query\Bookmark as BookmarkQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark as BookmarkQueryHandler;

return [
    'router' => [
        'routes' => include(__DIR__ . '/../../../vendor/olcs/olcs-transfer/config/backend-routes.config.php')
    ],
    'service_manager' => [
        'alias' => [
            'PublicationContextPlugin' => \Dvsa\Olcs\Api\Service\Publication\Context\PluginManager::class,
            'PublicationProcessPlugin' => \Dvsa\Olcs\Api\Service\Publication\Process\PluginManager::class
        ],
        'invokables' => [
            'Document' => \Dvsa\Olcs\Api\Service\Document\Document::class,
            'DocumentGenerator' => \Dvsa\Olcs\Api\Service\Document\DocumentGenerator::class,
            'DateService' => \Dvsa\Olcs\Api\Service\Date::class,
            'FileUploader' => \Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader::class,
            'RestrictionService' => \Dvsa\Olcs\Api\Service\Lva\RestrictionService::class,
            'SectionConfig' =>  \Dvsa\Olcs\Api\Service\Lva\SectionConfig::class,
        ],
        'factories' => [
            'SectionAccessService' => \Dvsa\Olcs\Api\Service\Lva\SectionAccessService::class,
            'ContentStore' => \Dvsa\Jackrabbit\Client\Service\ClientFactory::class,
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
            'CpmsIdentityProvider' => \Dvsa\Olcs\Api\Service\CpmsIdentityProviderFactory::class,
            'CpmsHelperService' => \Dvsa\Olcs\Api\Service\CpmsHelperService::class,
            'FeesHelperService' => \Dvsa\Olcs\Api\Service\FeesHelperService::class,
            \Dvsa\Olcs\Api\Service\Publication\PublicationGenerator::class =>
                \Dvsa\Olcs\Api\Service\Publication\PublicationGeneratorFactory::class,
            \Dvsa\Olcs\Api\Service\Publication\Context\PluginManager::class =>
                \Dvsa\Olcs\Api\Service\Publication\Context\PluginManagerFactory::class,
            \Dvsa\Olcs\Api\Service\Publication\Process\PluginManager::class =>
                \Dvsa\Olcs\Api\Service\Publication\Process\PluginManagerFactory::class,
        ],
    ],
    'file_uploader' => [
        'default' => 'ContentStore',
        'config' => [
            'location' => 'documents',
            'defaultPath' => '[locale]/[doc_type_name]/[year]/[month]', // e.g. gb/publications/2015/03
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
        'factories' => require(__DIR__ . '/query-map.config.php')
    ],
    \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'withContactDetails' => QueryPartial\WithContactDetailsFactory::class,
            'withCase' => QueryPartial\WithCaseFactory::class,
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
            'filterByLicence' => QueryPartial\Filter\ByLicence::class,
            'filterByApplication' => QueryPartial\Filter\ByApplication::class,
            'filterByBusReg' => QueryPartial\Filter\ByBusReg::class,
            'filterByIds' => QueryPartial\Filter\ByIds::class,
        ]
    ],
    \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::CONFIG_KEY => [
        'factories' => [
            'Application' => RepositoryFactory::class,
            'Address' => RepositoryFactory::class,
            'Appeal' => RepositoryFactory::class,
            'ContactDetails' => RepositoryFactory::class,
            'CompanySubsidiary' => RepositoryFactory::class,
            'Conviction' => RepositoryFactory::class,
            'Organisation' => RepositoryFactory::class,
            'Licence' => RepositoryFactory::class,
            'Bus' => RepositoryFactory::class,
            'BusRegHistory' => RepositoryFactory::class,
            'BusRegOtherService' => RepositoryFactory::class,
            'BusNoticePeriod' => RepositoryFactory::class,
            'BusShortNotice' => RepositoryFactory::class,
            'Trailer' => RepositoryFactory::class,
            'GracePeriod' => RepositoryFactory::class,
            'Task' => RepositoryFactory::class,
            'FeeType' => RepositoryFactory::class,
            'Fee' => RepositoryFactory::class,
            'Cases' => RepositoryFactory::class,
            'Pi' => \Dvsa\Olcs\Api\Domain\Repository\PiFactory::class,
            'NonPi' => RepositoryFactory::class,
            'EventHistory' => RepositoryFactory::class,
            'PublicHoliday' => RepositoryFactory::class,
            'Sla' => RepositoryFactory::class,
            'LicenceNoGen' => RepositoryFactory::class,
            'User' => RepositoryFactory::class,
            'PreviousConviction' => RepositoryFactory::class,
            'Prohibition' => RepositoryFactory::class,
            'ProhibitionDefect' => RepositoryFactory::class,
            'LegacyOffence' => RepositoryFactory::class,
            'Note' => RepositoryFactory::class,
            'TradingName' => RepositoryFactory::class,
            'IrfoGvPermit' => RepositoryFactory::class,
            'IrfoPermitStock' => RepositoryFactory::class,
            'IrfoPsvAuth' => RepositoryFactory::class,
            'IrfoPsvAuthNumber' => RepositoryFactory::class,
            'Impounding' => RepositoryFactory::class,
            'CommunityLic' => RepositoryFactory::class,
            'Workshop' => RepositoryFactory::class,
            'FinancialStandingRate' => RepositoryFactory::class,
            'Complaint' => RepositoryFactory::class,
            'PhoneContact' => RepositoryFactory::class,
            'OtherLicence' => RepositoryFactory::class,
            'Document' => RepositoryFactory::class,
            'Correspondence' => RepositoryFactory::class,
            'SystemParameter' => RepositoryFactory::class,
            'Stay' => RepositoryFactory::class,
            'TaskAllocationRule' => RepositoryFactory::class,
            'IrfoPartner' => RepositoryFactory::class,
            'Payment' => RepositoryFactory::class,
            'TransportManager' => RepositoryFactory::class,
            'DocParagraph' => RepositoryFactory::class,
            'Opposition' => RepositoryFactory::class,
            'Statement' => RepositoryFactory::class,
            'PublicationLink' => RepositoryFactory::class,
            'Publication' => RepositoryFactory::class,
            'GoodsDisc' => RepositoryFactory::class,
            'PsvDisc' => RepositoryFactory::class,
            'PiHearing' => RepositoryFactory::class,
            'PiVenue' => RepositoryFactory::class,
            'Recipient' => RepositoryFactory::class,
            'Partner' => RepositoryFactory::class,
            'TransportManagerApplication' => RepositoryFactory::class,
            'TransportManagerLicence' => RepositoryFactory::class,
            'Person' => RepositoryFactory::class,
            'ApplicationOperatingCentre' => RepositoryFactory::class,
            'LicenceOperatingCentre' => RepositoryFactory::class,
            'TmEmployment' => RepositoryFactory::class,
            'DocTemplate' => RepositoryFactory::class,
            'LicenceStatusRule' => RepositoryFactory::class,
            'LicenceVehicle' => RepositoryFactory::class,
            'CommunityLicSuspension' => RepositoryFactory::class,
            'CommunityLicSuspensionReason' => RepositoryFactory::class,
            'CommunityLicWithdrawal' => RepositoryFactory::class,
            'CommunityLicWithdrawalReason' => RepositoryFactory::class,
            'ConditionUndertaking' => RepositoryFactory::class,
            'OperatingCentre' => RepositoryFactory::class,
            'Category' => RepositoryFactory::class,
            'SubCategory' => RepositoryFactory::class,
            'SubCategoryDescription' => RepositoryFactory::class,
            'Scan' => RepositoryFactory::class,
            'BusRegSearchView' => RepositoryFactory::class,
            'ProposeToRevoke' => RepositoryFactory::class,
            'OrganisationPerson' => RepositoryFactory::class,
            'Vehicle' => RepositoryFactory::class,
            'VehicleHistoryView' => RepositoryFactory::class,
            'InspectionRequest' => RepositoryFactory::class,
            'CorrespondenceInbox' => RepositoryFactory::class,
            'SubmissionAction' => RepositoryFactory::class,
            'TrafficArea' => RepositoryFactory::class,
            'ChangeOfEntity' => RepositoryFactory::class,
            'ApplicationOrganisationPerson' => RepositoryFactory::class,
            'DocumentSearchView' => RepositoryFactory::class,
            'TaskSearchView' => RepositoryFactory::class,
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
    'publication_context' => [
        Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory::class,
        Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory::class,
        Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class
    ],
    'publication_process' => [
        Dvsa\Olcs\Api\Service\Publication\Process\Text1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Text1::class,
        Dvsa\Olcs\Api\Service\Publication\Process\HearingText1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\HearingText1::class,
    ],
    'publications' => [
        'HearingPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\HearingText1::class
            ],
        ),
    ]
];
