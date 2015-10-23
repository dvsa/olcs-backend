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
            'PublicationProcessPlugin' => \Dvsa\Olcs\Api\Service\Publication\Process\PluginManager::class,
        ],
        'invokables' => [
            'Document' => \Dvsa\Olcs\Api\Service\Document\Document::class,
            'DateService' => \Dvsa\Olcs\Api\Service\Date::class,
            'RestrictionService' => \Dvsa\Olcs\Api\Service\Lva\RestrictionService::class,
            'SectionConfig' =>  \Dvsa\Olcs\Api\Service\Lva\SectionConfig::class,
            'AddressFormatter' => \Dvsa\Olcs\Api\Service\Helper\FormatAddress::class,
            'VariationPublishValidationService' =>
                \Dvsa\Olcs\Api\Service\Lva\Variation\PublishValidationService::class,
        ],
        'factories' => [
            'FileUploader' => \Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader::class,
            'DocumentGenerator' => \Dvsa\Olcs\Api\Service\Document\DocumentGenerator::class,
            'DocumentNamingService' => \Dvsa\Olcs\Api\Service\Document\NamingService::class,
            'PsvVehiclesQueryHelper' => \Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper::class,
            'UpdateOperatingCentreHelper' => \Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper::class,
            'OperatingCentreHelper' => \Dvsa\Olcs\Api\Domain\Service\OperatingCentreHelper::class,
            'VariationOperatingCentreHelper' => \Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper::class,
            'SectionAccessService' => \Dvsa\Olcs\Api\Service\Lva\SectionAccessService::class,
            'ApplicationGrantValidationService' => \Dvsa\Olcs\Api\Service\Lva\Application\GrantValidationService::class,
            'ApplicationPublishValidationService' =>
                \Dvsa\Olcs\Api\Service\Lva\Application\PublishValidationService::class,
            'ContentStore' => \Dvsa\Olcs\DocumentShare\Service\ClientFactory::class,
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
            'CpmsHelperService' => \Dvsa\Olcs\Api\Service\CpmsHelperServiceFactory::class,
            'FeesHelperService' => \Dvsa\Olcs\Api\Service\FeesHelperService::class,
            'FinancialStandingHelperService' => \Dvsa\Olcs\Api\Service\FinancialStandingHelperService::class,

            \Dvsa\Olcs\Api\Service\Publication\PublicationGenerator::class =>
                \Dvsa\Olcs\Api\Service\Publication\PublicationGeneratorFactory::class,

            \Dvsa\Olcs\Api\Service\Publication\Context\PluginManager::class =>
                \Dvsa\Olcs\Api\Service\Publication\Context\PluginManagerFactory::class,

            \Dvsa\Olcs\Api\Service\Publication\Process\PluginManager::class =>
                \Dvsa\Olcs\Api\Service\Publication\Process\PluginManagerFactory::class,

            \Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator::class =>
                \Dvsa\Olcs\Api\Service\Submission\SubmissionGeneratorFactory::class,

            \Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManagerFactory::class
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
            'withApplication' => QueryPartial\WithApplicationFactory::class,
            'withBusReg' => QueryPartial\WithBusRegFactory::class,
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
            'Pi' => RepositoryFactory::class,
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
            'Submission ' => RepositoryFactory::class,
            'TaskAllocationRule' => RepositoryFactory::class,
            'IrfoPartner' => RepositoryFactory::class,
            'Transaction' => RepositoryFactory::class,
            'TransportManager' => RepositoryFactory::class,
            'DocParagraph' => RepositoryFactory::class,
            'Opposition' => RepositoryFactory::class,
            'Statement' => RepositoryFactory::class,
            'PublicationLink' => RepositoryFactory::class,
            'Publication' => RepositoryFactory::class,
            'GoodsDisc' => RepositoryFactory::class,
            'PsvDisc' => RepositoryFactory::class,
            'PiHearing' => RepositoryFactory::class,
            'Recipient' => RepositoryFactory::class,
            'Partner' => RepositoryFactory::class,
            'TransportManagerApplication' => RepositoryFactory::class,
            'TransportManagerLicence' => RepositoryFactory::class,
            'Person' => RepositoryFactory::class,
            'ApplicationOperatingCentre' => RepositoryFactory::class,
            'LicenceOperatingCentre' => RepositoryFactory::class,
            'TmCaseDecision' => RepositoryFactory::class,
            'TmEmployment' => RepositoryFactory::class,
            'TmQualification' => RepositoryFactory::class,
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
            'SubmissionSectionComment' => RepositoryFactory::class,
            'TrafficArea' => RepositoryFactory::class,
            'ChangeOfEntity' => RepositoryFactory::class,
            'ApplicationOrganisationPerson' => RepositoryFactory::class,
            'DocumentSearchView' => RepositoryFactory::class,
            'S4' => RepositoryFactory::class,
            'TaskSearchView' => RepositoryFactory::class,
            'PrivateHireLicence' => RepositoryFactory::class,
            'Continuation' => RepositoryFactory::class,
            'ContinuationDetail' => RepositoryFactory::class,
            'CompaniesHouseAlert' => RepositoryFactory::class,
            'CompaniesHouseCompany' => RepositoryFactory::class,
            'Queue' => RepositoryFactory::class,
            'AdminAreaTrafficArea' => RepositoryFactory::class,
            'PostcodeEnforcementArea' => RepositoryFactory::class,
            'PiVenue' => RepositoryFactory::class,
            'Disqualification' => RepositoryFactory::class,
            'DiscSequence' => RepositoryFactory::class,
            'EbsrSubmission' => RepositoryFactory::class,
            'TxcInbox' => RepositoryFactory::class,
            'OrganisationUser' => RepositoryFactory::class,
            'Role' => RepositoryFactory::class,
            'ApplicationReadAudit' => RepositoryFactory::class,
            'LicenceReadAudit' => RepositoryFactory::class,
            'OrganisationReadAudit' => RepositoryFactory::class,
            'BusRegReadAudit' => RepositoryFactory::class,
            'TransportManagerReadAudit' => RepositoryFactory::class,
            'CasesReadAudit' => RepositoryFactory::class,
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
            'can-update-licence-licence-type' => \Dvsa\Olcs\Api\Assertion\Licence\UpdateLicenceType::class,
            'can-manage-user-selfserve' => \Dvsa\Olcs\Api\Assertion\User\ManageUserSelfserve::class,
            'can-read-user-selfserve' => \Dvsa\Olcs\Api\Assertion\User\ReadUserSelfserve::class,
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
        Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousApplicationPublicationNo::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\AbstractFactory::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
        Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName::class,
        Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceTypes::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceTypes::class,
        Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class,
        Dvsa\Olcs\Api\Service\Publication\Context\BusReg\VariationReasons::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\BusReg\VariationReasons::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Application\BusNote::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Application\BusNote::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Application\ConditionUndertaking::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Application\ConditionUndertaking::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Application\LicenceCancelled::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Application\LicenceCancelled::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Application\OperatingCentres::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Application\OperatingCentres::class,
        Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers::class =>
            Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers::class,
    ],
    'publication_process' => [
        Dvsa\Olcs\Api\Service\Publication\Process\Text1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Text1::class,
        Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\HearingText1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\HearingText1::class,
        Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\DecisionText1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\DecisionText1::class,
        Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmHearingText1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmHearingText1::class,
        Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
        Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantNewText3::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantNewText3::class,
        Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantVarText3::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantVarText3::class,
        Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantCancelText3::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantCancelText3::class,
        Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1::class,
        Dvsa\Olcs\Api\Service\Publication\Process\Application\Text2::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Application\Text2::class,
        Dvsa\Olcs\Api\Service\Publication\Process\Application\Text3::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Application\Text3::class,
        Dvsa\Olcs\Api\Service\Publication\Process\Police::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Police::class,
        Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class,
        Dvsa\Olcs\Api\Service\Publication\Process\Licence\Text1::class =>
            Dvsa\Olcs\Api\Service\Publication\Process\Licence\Text1::class,
    ],
    'publications' => [
        'LicencePublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\BusNote::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousLicencePublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\TransportManagers::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceCancelled::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Text3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class,
            ],
        ),
        'ApplicationPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousApplicationPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\ConditionUndertaking::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\OperatingCentres::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\People::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Text3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class
            ],
        ),
        'VariationPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousApplicationPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Variation\ConditionUndertaking::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Variation\OperatingCentres::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\People::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Variation\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Variation\Text3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class,
            ],
        ),
        'Schedule41TruePublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Application\People::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Schedule41\Text1::class,
            ],
        ),
        'Schedule41UntruePublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Variation\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Schedule41\Text3::class
            ],
        ),
        'HearingPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\HearingText1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Police::class
            ],
        ),
        'HearingDecision' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\DecisionText1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Police::class
            ],
        ),
        'TmHearingPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmHearingText1::class
            ],
        ),
        'TmHearingDecision' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PiVenue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmHearingText1::class
            ],
        ),
        'BusGrantNew' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceTypes::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantNewText3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Police::class
            ],
        ),
        'BusGrantVariation' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\VariationReasons::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantVarText3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Police::class
            ],
        ),
        'BusGrantCancel' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantCancelText3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Police::class
            ],
        ),
    ],
    'submissions' => require(__DIR__ . '/submissions.config.php')
];
