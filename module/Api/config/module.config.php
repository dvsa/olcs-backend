<?php

use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryFactory;
use Dvsa\Olcs\Api\Domain\QueryPartial;
use Dvsa\Olcs\Api\Domain\Util;
use Dvsa\Olcs\Api\Service as ApiSrv;

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
            'DoctrineLogger' => Util\DoctrineExtension\Logger::class,
            'QaDirectOptionsGenerator' => ApiSrv\Qa\FormControlStrategy\OptionsGenerator\Direct::class,
        ],
        'factories' => [
            'ConvertToPdf' => \Dvsa\Olcs\Api\Service\ConvertToPdf\WebServiceClientFactory::class,
            'FileUploader' => \Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader::class,
            'DocumentGenerator' => \Dvsa\Olcs\Api\Service\Document\DocumentGenerator::class,
            'DocumentNamingService' => \Dvsa\Olcs\Api\Service\Document\NamingService::class,
            'UpdateOperatingCentreHelper' => \Dvsa\Olcs\Api\Domain\Service\UpdateOperatingCentreHelper::class,
            'OperatingCentreHelper' => \Dvsa\Olcs\Api\Domain\Service\OperatingCentreHelper::class,
            'VariationOperatingCentreHelper' => \Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper::class,
            'SectionAccessService' => \Dvsa\Olcs\Api\Service\Lva\SectionAccessService::class,
            'ApplicationGrantValidationService' => \Dvsa\Olcs\Api\Service\Lva\Application\GrantValidationService::class,
            'ApplicationPublishValidationService' =>
                \Dvsa\Olcs\Api\Service\Lva\Application\PublishValidationService::class,
            'ContentStore' => \Dvsa\Olcs\DocumentShare\Service\ClientFactory::class,
            'PayloadValidationListener' => \Dvsa\Olcs\Api\Mvc\PayloadValidationListenerFactory::class,
            'CommandHandlerManager' => \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::class,
            'QueryHandlerManager' => \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::class,
            'ValidationHandlerManager' => \Dvsa\Olcs\Api\Domain\ValidationHandlerManagerFactory::class,
            'DomainValidatorManager' => \Dvsa\Olcs\Api\Domain\ValidatorManagerFactory::class,
            'QueryPartialServiceManager' => \Dvsa\Olcs\Api\Domain\QueryPartialServiceManagerFactory::class,
            'RepositoryServiceManager' => \Dvsa\Olcs\Api\Domain\RepositoryServiceManagerFactory::class,
            'DbQueryServiceManager' => \Dvsa\Olcs\Api\Domain\DbQueryServiceManagerFactory::class,
            'QueryBuilder' => \Dvsa\Olcs\Api\Domain\QueryBuilderFactory::class,
            Util\SlaCalculatorInterface::class => Util\SlaCalculatorFactory::class,
            Util\TimeProcessorBuilderInterface::class => Util\TimeProcessorBuilderFactory::class,
            'TransactionManager' => \Dvsa\Olcs\Api\Domain\Repository\TransactionManagerFactory::class,
            'CpmsIdentityProvider' => \Dvsa\Olcs\Api\Service\CpmsIdentityProviderFactory::class,
            'CpmsHelperService' => \Dvsa\Olcs\Api\Service\CpmsV2HelperService::class,
            'FeesHelperService' => \Dvsa\Olcs\Api\Service\FeesHelperService::class,
            'FinancialStandingHelperService' => \Dvsa\Olcs\Api\Service\FinancialStandingHelperService::class,
            'CompaniesHouseService' => \Dvsa\Olcs\Api\Service\CompaniesHouseService::class,

            \Dvsa\Olcs\Api\Service\Publication\PublicationGenerator::class =>
                \Dvsa\Olcs\Api\Service\Publication\PublicationGeneratorFactory::class,

            \Dvsa\Olcs\Api\Service\Publication\Context\PluginManager::class =>
                \Dvsa\Olcs\Api\Service\Publication\Context\PluginManagerFactory::class,

            \Dvsa\Olcs\Api\Service\Publication\Process\PluginManager::class =>
                \Dvsa\Olcs\Api\Service\Publication\Process\PluginManagerFactory::class,

            \Dvsa\Olcs\Api\Service\OpenAm\ClientInterface::class => \Dvsa\Olcs\Api\Service\OpenAm\ClientFactory::class,
            \Dvsa\Olcs\Api\Service\OpenAm\UserInterface::class => \Dvsa\Olcs\Api\Service\OpenAm\UserFactory::class,

            \Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator::class =>
                \Dvsa\Olcs\Api\Service\Submission\SubmissionGeneratorFactory::class,

            \Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager::class =>
                \Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManagerFactory::class,

            \Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClientFactory::class,
            \Dvsa\Olcs\Api\Rbac\PidIdentityProvider::class => \Dvsa\Olcs\Api\Rbac\PidIdentityProviderFactory::class,

            'TransExchangeXmlMapping' =>
                \Dvsa\Olcs\Api\Service\Ebsr\Mapping\TransExchangeXmlFactory::class,
            'TransExchangePublisherXmlMapping' =>
                \Dvsa\Olcs\Api\Service\Ebsr\Mapping\TransExchangePublisherXmlFactory::class,

            \Dvsa\Olcs\Api\Service\Ebsr\FileProcessorInterface::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\FileProcessorFactory::class,

            \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\XmlStructureInputFactory::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\XmlStructureInputFactory::class,
            \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\BusRegistrationInputFactory::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\BusRegistrationInputFactory::class,
            \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ProcessedDataInputFactory::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ProcessedDataInputFactory::class,
            \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ShortNoticeInputFactory::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\InputFilter\ShortNoticeInputFactory::class,
            'TrafficAreaValidator' => \Dvsa\Olcs\Api\Domain\Service\TrafficAreaValidator::class,

            'ComplianceEpisodeInput' => \Dvsa\Olcs\Api\Service\Nr\InputFilter\ComplianceEpisodeInputFactory::class,
            'ComplianceXmlStructure' => \Dvsa\Olcs\Api\Service\Nr\InputFilter\XmlStructureInputFactory::class,
            'SeriousInfringementInput' => \Dvsa\Olcs\Api\Service\Nr\InputFilter\SeriousInfringementInputFactory::class,
            'ComplianceEpisodeXmlMapping' => \Dvsa\Olcs\Api\Service\Nr\Mapping\ComplianceEpisodeXmlFactory::class,

            \Dvsa\Olcs\Api\Service\Nr\InrClientInterface::class => Dvsa\Olcs\Api\Service\Nr\InrClientFactory::class,
            \Dvsa\Olcs\Api\Service\Nr\MsiResponse::class => \Dvsa\Olcs\Api\Service\Nr\MsiResponseFactory::class,

            \Dvsa\Olcs\Api\Mvc\OlcsBlameableListener::class => \Dvsa\Olcs\Api\Mvc\OlcsBlameableListenerFactory::class,
            \Dvsa\Olcs\Api\Listener\OlcsEntityListener::class => \Dvsa\Olcs\Api\Listener\OlcsEntityListener::class,

            ApiSrv\Nysiis\NysiisSoapClient::class => ApiSrv\Nysiis\NysiisSoapClientFactory::class,
            ApiSrv\Nysiis\NysiisRestClient::class => ApiSrv\Nysiis\NysiisRestClientFactory::class,

            ApiSrv\Document\PrintLetter::class => ApiSrv\Document\PrintLetter::class,
            \Dvsa\Olcs\Api\Service\Toggle\ToggleService::class =>
                \Dvsa\Olcs\Api\Service\Toggle\ToggleServiceFactory::class,

            'TemplateFetcher' => ApiSrv\Template\TemplateFetcher::class,
            'TemplateDatabaseTwigLoader' => ApiSrv\Template\DatabaseTwigLoaderFactory::class,
            'TemplateDatabaseTemplateFetcher' => ApiSrv\Template\DatabaseTemplateFetcherFactory::class,
            'TemplateTwigRenderer' => ApiSrv\Template\TwigRendererFactory::class,
            'TemplateTwigEnvironment' => ApiSrv\Template\TwigEnvironmentFactory::class,
            'TemplateStrategySelectingViewRenderer' => ApiSrv\Template\StrategySelectingViewRendererFactory::class,

            'QaFormControlStrategyProvider' => ApiSrv\Qa\FormControlStrategyProviderFactory::class,
            'QaRadioFormControlStrategy' => ApiSrv\Qa\FormControlStrategy\RadioFactory::class,
            'QaCheckboxFormControlStrategy' => ApiSrv\Qa\FormControlStrategy\CheckboxFactory::class,
            'QaTextFormControlStrategy' => ApiSrv\Qa\FormControlStrategy\TextFactory::class,
            'QaEcmtRemovalNoOfPermitsFormControlStrategy' =>
                ApiSrv\Qa\FormControlStrategy\EcmtRemovalNoOfPermitsFactory::class,

            'QaGenericAnswerSaver' => ApiSrv\Qa\FormControlStrategy\Common\GenericAnswerSaverFactory::class,
            'QaOptionsGeneratorProvider' => ApiSrv\Qa\FormControlStrategy\OptionsGeneratorProviderFactory::class,
            'QaApplicationStepObjectsProvider' => ApiSrv\Qa\ApplicationStepObjectsProviderFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'submission' => __DIR__ . '/../view/submission',
        ]
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
            'Api\Xml' => \Dvsa\Olcs\Api\Controller\XmlController::class
        ]
    ],
    \Dvsa\Olcs\Api\Domain\DbQueryServiceManagerFactory::CONFIG_KEY => include(__DIR__ . '/db-query-map.config.php'),
    \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::CONFIG_KEY => [
        'factories' => require(__DIR__ . '/command-map.config.php')
    ],
    \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::CONFIG_KEY => [
        'factories' => require(__DIR__ . '/query-map.config.php')
    ],
    \Dvsa\Olcs\Api\Domain\ValidationHandlerManagerFactory::CONFIG_KEY => [
        'factories' => require(__DIR__ . '/validation-map.config.php')
    ],
    \Dvsa\Olcs\Api\Domain\ValidatorManagerFactory::CONFIG_KEY => require(__DIR__ . '/validators.config.php'),
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
            'filterByPermitApplication' => QueryPartial\Filter\ByPermitApplication::class,
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
            'Decision' => RepositoryFactory::class,
            'Organisation' => RepositoryFactory::class,
            'Licence' => RepositoryFactory::class,
            'Bus' => RepositoryFactory::class,
            'BusRegHistory' => RepositoryFactory::class,
            'BusRegOtherService' => RepositoryFactory::class,
            'BusNoticePeriod' => RepositoryFactory::class,
            'BusShortNotice' => RepositoryFactory::class,
            'BusServiceType' => RepositoryFactory::class,
            'LocalAuthority' => RepositoryFactory::class,
            'Trailer' => RepositoryFactory::class,
            'GracePeriod' => RepositoryFactory::class,
            'Task' => RepositoryFactory::class,
            'FeeType' => RepositoryFactory::class,
            'Fee' => RepositoryFactory::class,
            'Cases' => RepositoryFactory::class,
            'Pi' => RepositoryFactory::class,
            'NonPi' => RepositoryFactory::class,
            'EventHistory' => RepositoryFactory::class,
            'EventHistoryType' => RepositoryFactory::class,
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
            'IrfoGvPermitType' => RepositoryFactory::class,
            'IrfoPermitStock' => RepositoryFactory::class,
            'IrfoPsvAuth' => RepositoryFactory::class,
            'IrfoPsvAuthType' => RepositoryFactory::class,
            'IrfoPsvAuthNumber' => RepositoryFactory::class,
            'IrfoCountry' => RepositoryFactory::class,
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
            'FeatureToggle' => RepositoryFactory::class,
            'Stay' => RepositoryFactory::class,
            'Submission ' => RepositoryFactory::class,
            'TaskAllocationRule' => RepositoryFactory::class,
            'TaskAlphaSplit' => RepositoryFactory::class,
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
            'BusRegBrowseView' => RepositoryFactory::class,
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
            'Venue' => RepositoryFactory::class,
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
            'Team' => RepositoryFactory::class,
            'TeamPrinter' => RepositoryFactory::class,
            'Printer' => RepositoryFactory::class,
            'ErruRequest' => RepositoryFactory::class,
            'ErruRequestFailure' => RepositoryFactory::class,
            'SeriousInfringement' => RepositoryFactory::class,
            'SiPenalty' => RepositoryFactory::class,
            'SiCategory' => RepositoryFactory::class,
            'SiCategoryType' => RepositoryFactory::class,
            'SiPenaltyRequestedType' => RepositoryFactory::class,
            'SiPenaltyImposedType' => RepositoryFactory::class,
            'SiPenaltyType' => RepositoryFactory::class,
            'Country' => RepositoryFactory::class,
            'PresidingTc' => RepositoryFactory::class,
            'RefData' => RepositoryFactory::class,
            'HistoricTm' => RepositoryFactory::class,
            'SlaTargetDate' => RepositoryFactory::class,
            'ViOcView' => RepositoryFactory::class,
            'ViOpView' => RepositoryFactory::class,
            'ViTnmView' => RepositoryFactory::class,
            'ViVhlView' => RepositoryFactory::class,
            'SystemInfoMessage' => RepositoryFactory::class,
            'Reason' => RepositoryFactory::class,
            'PiDefinition' => RepositoryFactory::class,
            'DataGovUk' => Repository\Factory\DataGovUkFactory::class,
            'DataDvaNi' => Repository\Factory\DataDvaNiFactory::class,
            'CompanyHouseVsOlcsDiffs' => Repository\Factory\CompaniesHouseVsOlcsDiffsFactory::class,
            'DigitalSignature' => RepositoryFactory::class,
            'DataRetentionRule' => RepositoryFactory::class,
            'DataRetention' => RepositoryFactory::class,
            'DataService' => RepositoryFactory::class,
            'DocumentToDelete' => RepositoryFactory::class,
            'Hearing' => RepositoryFactory::class,
            'GetDbValue' => RepositoryFactory::class,
            'Surrender' => RepositoryFactory::class,
            'Sectors' => RepositoryFactory::class,
            'EcmtPermits' => RepositoryFactory::class,
            'EcmtPermitApplication' => RepositoryFactory::class,
            'IrhpPermitApplication' => RepositoryFactory::class,
            'IrhpApplication' => RepositoryFactory::class,
            'IrhpApplicationView' => RepositoryFactory::class,
            'IrhpCandidatePermit' => RepositoryFactory::class,
            'IrhpPermit' => RepositoryFactory::class,
            'IrhpPermitJurisdictionQuota' => RepositoryFactory::class,
            'IrhpPermitSectorQuota' => RepositoryFactory::class,
            'IrhpPermitStock' => RepositoryFactory::class,
            'IrhpPermitType' => RepositoryFactory::class,
            'IrhpPermitWindow' => RepositoryFactory::class,
            'IrhpPermitRange' => RepositoryFactory::class,
            'IrhpApplication' => RepositoryFactory::class,
            'Template' => RepositoryFactory::class,
            'ApplicationStep' => RepositoryFactory::class,
            'ApplicationPath' => RepositoryFactory::class,
            'Answer' => RepositoryFactory::class,
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
                    \Dvsa\Olcs\Api\Listener\OlcsEntityListener::class,
                    \Gedmo\SoftDeleteable\SoftDeleteableListener::class,
                    \Gedmo\Translatable\TranslatableListener::class,
                    \Dvsa\Olcs\Api\Mvc\OlcsBlameableListener::class,
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'filters' => [
                    'soft-deleteable' => \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter::class,
                ],
                'types' => [
                    'yesno' => 'Dvsa\Olcs\Api\Entity\Types\YesNoType',
                    'yesnonull' => 'Dvsa\Olcs\Api\Entity\Types\YesNoNullType',
                    'date' => 'Dvsa\Olcs\Api\Entity\Types\DateType',
                    'datetime' => 'Dvsa\Olcs\Api\Entity\Types\DateTimeType',
                    'encrypted_string' => \Dvsa\Olcs\Api\Entity\Types\EncryptedStringType::class
                ]
            ]
        ]
    ],
    'zfc_rbac' => [
        'identity_provider' => \Dvsa\Olcs\Api\Rbac\PidIdentityProvider::class,
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
        Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class =>
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
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class
            ],
        ),
        'Schedule41UntruePublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\TransportManagers::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\People::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Variation\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Schedule41\Text3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class
            ],
        ),
        'HearingPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\HearingText1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class
            ],
        ),
        'HearingDecision' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\DecisionText1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class
            ],
        ),
        'TmHearingPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName::class,
                Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\Person::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmHearingText1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\TransportManager\Police::class
            ],
        ),
        'TmHearingDecision' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Publication\PreviousPublicationNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\TransportManagerName::class,
                Dvsa\Olcs\Api\Service\Publication\Context\TransportManager\Person::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmHearingText1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\TransportManager\Police::class
            ],
        ),
        'BusGrantNew' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceTypes::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantNewText3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class
            ],
        ),
        'BusGrantVariation' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\VariationReasons::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantVarText3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class
            ],
        ),
        'BusGrantCancel' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
                Dvsa\Olcs\Api\Service\Publication\Context\BusReg\ServiceDesignation::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\BusReg\GrantCancelText3::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class
            ],
        ),
        'ImpoundingLicencePublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\People::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Impounding\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Impounding\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police::class
            ],
        ),
        'ImpoundingApplicationPublication' => array(
            'context' => [
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\Venue::class,
                Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\HearingDate::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceNo::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Application\People::class,
                Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceAddress::class,
            ],
            'process' => [
                Dvsa\Olcs\Api\Service\Publication\Process\Impounding\Text1::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Impounding\Text2::class,
                Dvsa\Olcs\Api\Service\Publication\Process\Application\Police::class
            ],
        ),
    ],
    'submissions' => require(__DIR__ . '/submissions.config.php'),
    'ebsr' => [
        'transexchange_publisher' => [
            'templates' => [
                \Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient::REQUEST_MAP_TEMPLATE =>
                    __DIR__ . '/../data/ebsr/requestmap_template.xml',
                \Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient::TIMETABLE_TEMPLATE =>
                    __DIR__ . '/../data/ebsr/timetable_template.xml',
                \Dvsa\Olcs\Api\Service\Ebsr\TransExchangeClient::DVSA_RECORD_TEMPLATE =>
                    __DIR__ . '/../data/ebsr/dvsarecord_template.xml'
            ]
        ],
    ],
    'xsd_mappings' =>[
        'http://www.w3.org/2001/xml.xsd' => __DIR__ . '/../data/ebsr/xsd/xml.xsd',
        'http://www.transxchange.org.uk/schema/2.1/TransXChange_registration.xsd' =>
            __DIR__ . '/../data/ebsr/xsd/TransXChange_schema_2.1/TransXChange_registration.xsd',
        'http://www.transxchange.org.uk/schema/2.4/TransXChange_registration.xsd' =>
            __DIR__ . '/../data/ebsr/xsd/TransXChange_schema_2.4/TransXChange_registration.xsd',
        'http://www.transxchange.org.uk/schema/2.5/TransXChange_registration.xsd' =>
            __DIR__ . '/../data/ebsr/xsd/TransXChange_schema_2.5/TransXChange_registration.xsd',
        'http://naptan.dft.gov.uk/transxchange/publisher/schema/3.1.2/TransXChangePublisherService.xsd' =>
            __DIR__ . '/../data/ebsr/xsd/TransXChange_schema_2.4/TransXChangePublisherService_2_4.xsd',
        'https://webgate.ec.testa.eu/erru/1.0' => __DIR__ . '/../data/nr/xsd/ERRU2MS_Infringement_Req.xsd'
    ],
    'validators' => [
        'invokables' => [
            \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\Operator::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\Operator::class,
            \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\Registration::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\Registration::class,
            \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\ServiceClassification::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\ServiceClassification::class,
            \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\SupportingDocuments::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\XmlValidator\SupportingDocuments::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\EffectiveDate::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\EffectiveDate::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\EndDate::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\EndDate::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ApplicationType::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ApplicationType::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\Licence::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\Licence::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ServiceNo::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ServiceNo::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\LocalAuthorityMissing::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\LocalAuthorityMissing::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\NewAppAlreadyExists::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\NewAppAlreadyExists::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\BusRegNotFound::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\BusRegNotFound::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\RegisteredBusRoute::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\RegisteredBusRoute::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\VariationNumber::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\VariationNumber::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingSection::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingSection::class,
            \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ShortNotice\MissingReason::class,
            \Dvsa\Olcs\Api\Service\Nr\Validator\SiPenaltyImposedDate::class =>
                \Dvsa\Olcs\Api\Service\Nr\Validator\SiPenaltyImposedDate::class
        ],
        'aliases' => [
            'Rules\ProcessedData\LocalAuthorityMissing' =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\LocalAuthorityMissing::class,
            'Rules\ProcessedData\NewAppAlreadyExists' =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\NewAppAlreadyExists::class,
            'Rules\ProcessedData\BusRegNotFound' =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\BusRegNotFound::class,
            'Rules\ProcessedData\RegisteredBusRoute' =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\RegisteredBusRoute::class,
            'Rules\ProcessedData\VariationNumber' =>
                \Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\ProcessedData\VariationNumber::class,
        ]
    ],
    'filters' => [
        'invokables' => [
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\NoticePeriod::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\NoticePeriod::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectIsTxcApp::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectIsTxcApp::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectNaptanCodes::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectNaptanCodes::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectReceivedDate::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectReceivedDate::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\Subsidy::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\Subsidy::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\Via::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\Via::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\ExistingRegNo::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\ExistingRegNo::class,
            \Dvsa\Olcs\Api\Service\Nr\Filter\Format\IsExecuted::class =>
                \Dvsa\Olcs\Api\Service\Nr\Filter\Format\IsExecuted::class,
            \Dvsa\Olcs\Api\Service\Nr\Filter\Format\SiDates::class =>
                \Dvsa\Olcs\Api\Service\Nr\Filter\Format\SiDates::class,
            \Dvsa\Olcs\Api\Service\Nr\Filter\Format\MemberStateCode::class =>
                \Dvsa\Olcs\Api\Service\Nr\Filter\Format\MemberStateCode::class,
            \Dvsa\Olcs\Api\Service\Nr\Filter\LicenceNumber::class =>
                \Dvsa\Olcs\Api\Service\Nr\Filter\LicenceNumber::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\MiscSnJustification::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\MiscSnJustification::class,
            \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\SubmissionResult::class =>
                \Dvsa\Olcs\Api\Service\Ebsr\Filter\Format\SubmissionResult::class
        ],
        'factories' => [
            \Dvsa\Olcs\Api\Service\Nr\Filter\Vrm::class => \Dvsa\Olcs\Api\Service\Nr\Filter\VrmFactory::class
        ],
    ],
];
