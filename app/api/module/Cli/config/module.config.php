<?php

use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Cli\Domain\CommandHandler;
use Dvsa\Olcs\Cli\Domain\Command;
use Dvsa\Olcs\Cli\Domain\Query;
use Dvsa\Olcs\Cli\Domain\QueryHandler;
use Dvsa\Olcs\Cli;

return [
    'console' => [
        'router' => [
            'routes' => [
                'diagnostic' => [
                    'options' => [
                        'route' => 'diagnostic [--skip=] [--openam-user=] [--email=] [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\DiagnosticController::class,
                            'action' => 'index'
                        ],
                    ],
                ],
                'check-fk-integrity' => [
                    'options' => [
                        'route' => 'check-fk-integrity',
                        'defaults' => [
                            'controller' => Cli\Controller\DiagnosticController::class,
                            'action' => 'checkFkIntegrity',
                        ],
                    ],
                ],
                'check-fk-integrity-sql' => [
                    'options' => [
                        'route' => 'check-fk-integrity-sql',
                        'defaults' => [
                            'controller' => Cli\Controller\DiagnosticController::class,
                            'action' => 'checkFkIntegritySql',
                        ],
                    ],
                ],
                'licence-status-rules' => [
                    'options' => [
                        'route' => 'licence-status-rules [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'licenceStatusRules'
                        ],
                    ],
                ],
                'enqueue-ch-compare' => [
                    'options' => [
                        'route' => 'enqueue-ch-compare [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'enqueueCompaniesHouseCompare',
                        ],
                    ],
                ],
                'ch-vs-olcs-compare' => [
                    'options' => [
                        'route' => 'ch-vs-olcs-diffs [--verbose|-v] [--path=]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'companiesHouseVsOlcsDiffsExport',
                        ],
                    ],
                ],
                'duplicate-vehicle-warning' => [
                    'options' => [
                        'route' => 'duplicate-vehicle-warning [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'duplicateVehicleWarning',
                        ],
                    ],
                ],
                'duplicate-vehicle-removal' => [
                    'options' => [
                        'route' => 'duplicate-vehicle-removal [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'duplicateVehicleRemoval',
                        ],
                    ],
                ],
                'batch-cns' => [
                    'options' => [
                        'route' => 'batch-cns [--verbose|-v] [--dryrun|-d]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'continuationNotSought'
                        ],
                    ],
                ],
                'batch-clean-variations' => [
                    'options' => [
                        'route' => 'batch-clean-variations [--verbose|-v] [--dryrun|-d]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'cleanUpVariations'
                        ],
                    ],
                ],
                'get-db-value' => [
                    'options' => [
                        'route' => 'get-db-value [--property-name=] [--entity-name=] [--filter-property=] [--filter-value=] [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\UtilController::class,
                            'action' => 'getDbValue'
                        ],
                    ],
                ],
                'process-queue' => [
                    'options' => [
                        'route' => 'process-queue [--type=] [--exclude=] [--queue-duration=]',
                        'defaults' => [
                            'controller' => Cli\Controller\QueueController::class,
                            'action' => 'index'
                        ],
                    ],
                ],
                'process-inbox' => [
                    'options' => [
                        'route' => 'process-inbox [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'processInboxDocuments'
                        ],
                    ],
                ],
                'process-ntu' => [
                    'options' => [
                        'route' => 'process-ntu [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'processNtu'
                        ],
                    ],
                ],
                'inspection-request-email' => [
                    'options' => [
                        'route' => 'inspection-request-email [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'inspectionRequestEmail'
                        ],
                    ],
                ],
                'remove-read-audit' => [
                    'options' => [
                        'route' => 'remove-read-audit [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'removeReadAudit'
                        ],
                    ],
                ],
                'system-parameter' => [
                    'options' => [
                        'route' => 'system-parameter <name> <value> [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'setSystemParameter'
                        ],
                    ],
                ],
                'resolve-payments' => [
                    'options' => [
                        'route' => 'resolve-payments [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'resolvePayments',
                        ],
                    ],
                ],
                'create-vi-extract-files' => [
                    'options' => [
                        'route' =>
                            'create-vi-extract-files [--verbose|-v] [--oc|-oc] ' .
                            '[--op|-op] [--tnm|-tnm] [--vhl|-vhl] [--all|-all] [--path=]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'createViExtractFiles',
                        ],
                    ],
                ],

                'export-to-data-gov-uk' => [
                    'options' => [
                        'route' => 'data-gov-uk-export <report-name> [--verbose|-v] [--path=]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'dataGovUkExport',
                        ],
                    ],
                ],
                'export-data-for-northern-ireland' => [
                    'options' => [
                        'route' => 'data-dva-ni-export <report-name> [--verbose|-v] [--path=]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'dataDvaNiExport',
                        ],
                    ],
                ],
                'process-cl' => [
                    'options' => [
                        'route' => 'process-cl [--verbose|-v] [--dryrun|-d]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'processCommunityLicences'
                        ],
                    ],
                ],
                'flag-urgent-tasks' => [
                    'options' => [
                        'route' => 'flag-urgent-tasks [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'flagUrgentTasks'
                        ],
                    ],
                ],
                'expire-bus-registration' => [
                    'options' => [
                        'route' => 'expire-bus-registration [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'expireBusRegistration'
                        ],
                    ],
                ],
                'ecmt-withdraw-unpaid' => [
                    'options' => [
                        'route' => 'ecmt-withdraw-unpaid [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'withdrawUnpaidEcmtApplications',
                        ],
                    ],
                ],
                'import-users-from-csv' => [
                    'options' => [
                        'route' => 'import-users-from-csv <csv-path> [--result-csv-path=] [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'importUsersFromCsv',
                        ],
                    ],
                ],
                'data-retention-rule' => [
                    'options' => [
                        'route' => 'data-retention-rule (populate|delete) [--limit=] [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'dataRetentionRule',
                        ],
                    ],
                ],
                'digital-continuation-reminders' => [
                    'options' => [
                        'route' => 'digital-continuation-reminders [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'digitalContinuationReminders',
                        ],
                    ],
                ],
                'create-psv-licence-surrender-tasks' => [
                    'options' => [
                        'route' => 'create-psv-licence-surrender-tasks [--verbose|-v] [--dryrun|-d]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'createPsvLicenceSurrenderTasks',
                        ],
                    ],
                ],
                'database-maintenance' => [
                    'options' => [
                        'route' => 'database-maintenance [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'databaseMaintenance',
                        ],
                    ],
                ],
                'last-tm-letter' => [
                    'options' => [
                        'route' => 'last-tm-letter [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'lastTmLetter',
                        ],
                    ],
                ],
                'permits' => [
                    'options' => [
                        'route' => 'permits (close-expired-windows) [--since=<date>] [--verbose|-v]',
                        'defaults' => [
                            'controller' => Cli\Controller\BatchController::class,
                            'action' => 'permits',
                        ],
                    ],
                ],
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            Cli\Controller\BatchController::class => Cli\Controller\BatchController::class,
            Cli\Controller\QueueController::class => Cli\Controller\QueueController::class,
            Cli\Controller\DiagnosticController::class => Cli\Controller\DiagnosticController::class,
            Cli\Controller\UtilController::class => Cli\Controller\UtilController::class,
        ]
    ],
    'cache' => [
        'adapter' => [
            // apc_cli is not currently enabled in environments therefore change it
            'name' => 'memory',
        ]
    ],
    'service_manager' => [
        'alias' => [
            'NysiisService' => 'Dvsa\Olcs\Api\Service\Data\Nysiis'
        ],
        'invokables' => [
            'Queue' => Dvsa\Olcs\Cli\Service\Queue\QueueProcessor::class,
        ],
        'factories' => [
            'MessageConsumerManager' => \Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManagerFactory::class,
            'Dvsa\Olcs\Api\Service\Data\Nysiis' => Dvsa\Olcs\Api\Service\Data\NysiisFactory::class,
        ],
    ],
    'message_consumer_manager' => [
        'invokables' => [
            Queue::TYPE_COMPANIES_HOUSE_INITIAL
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse\InitialDataLoad::class,
            Queue::TYPE_COMPANIES_HOUSE_COMPARE
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse\Compare::class,
            Queue::TYPE_CONT_CHECKLIST
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklist::class,
            Queue::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklistReminderGenerateLetter::class,
            Queue::TYPE_TM_SNAPSHOT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Tm\Snapshot::class,
            Queue::TYPE_CPMS_REPORT_DOWNLOAD
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Cpms\ReportDownload::class,
            Queue::TYPE_EBSR_REQUEST_MAP
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Ebsr\RequestMap::class,
            Queue::TYPE_EBSR_PACK
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Ebsr\ProcessPack::class,
            Queue::TYPE_EBSR_PACK_FAILED
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Ebsr\ProcessPackFailed::class,
            Queue::TYPE_EMAIL
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Email\Send::class,
            Queue::TYPE_PRINT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\PrintJob\PrintJob::class,
            Queue::TYPE_DISC_PRINTING_PRINT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\PrintJob\PrintJob::class,
            Queue::TYPE_DISC_PRINTING
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\DiscPrinting\PrintDiscs::class,
            Queue::TYPE_CREATE_GOODS_VEHICLE_LIST
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\DiscPrinting\CreateGoodsVehicleList::class,
            Queue::TYPE_CREATE_PSV_VEHICLE_LIST
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\DiscPrinting\CreatePsvVehicleList::class,
            Queue::TYPE_SEND_MSI_RESPONSE
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Nr\SendMsiResponse::class,
            Queue::TYPE_UPDATE_NYSIIS_TM_NAME
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Tm\UpdateTmNysiisName::class,
            Queue::TYPE_CNS
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Licence\ProcessContinuationNotSought::class,
            Queue::TYPE_CNS_EMAIL
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Licence\SendContinuationNotSought::class,
            Queue::TYPE_CREATE_COM_LIC
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\CommunityLicence\CreateForLicence::class,
            Queue::TYPE_REMOVE_DELETED_DOCUMENTS
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\RemoveDeleteDocuments::class,
            Queue::TYPE_CREATE_CONTINUATION_SNAPSHOT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationSnapshot::class,
            Queue::TYPE_CONT_DIGITAL_REMINDER
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationDigitalReminder::class,
            Queue::TYPE_PERMITS_ALLOCATE
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\AllocatePermits::class,
            Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\AllocateIrhpApplicationPermits::class,
            Queue::TYPE_RUN_ECMT_SCORING
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\RunScoring::class,
            Queue::TYPE_ACCEPT_ECMT_SCORING
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\AcceptScoring::class,
            Queue::TYPE_PERMIT_GENERATE
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Permits\GeneratePermits::class,
            Queue::TYPE_PERMIT_PRINT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\PrintJob\PrintJob::class,
            Queue::TYPE_COMM_LIC_BULK_REPRINT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\CommunityLicence\ReportingBulkReprint::class
        ],
        'factories' => [
            Queue::TYPE_CPID_EXPORT_CSV => Cli\Service\Queue\Consumer\Factory\CpidOrganisationExportFactory::class,
            Queue::TYPE_REFUND_INTERIM_FEES
            => Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory\RefundInterimFeesFactory::class
        ]
    ],
    'queue' => [
        //'isLongRunningProcess' => true,
        'runFor' => 60,
    ],
    'file-system' => [
        'path' => '/tmp'
    ],
    \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            Command\RemoveReadAudit::class => CommandHandler\RemoveReadAudit::class,
            Command\CleanUpAbandonedVariations::class => CommandHandler\CleanUpAbandonedVariations::class,
            Command\CreateViExtractFiles::class => CommandHandler\CreateViExtractFiles::class,
            Command\SetViFlags::class => CommandHandler\SetViFlags::class,
            Command\DataGovUkExport::class => CommandHandler\DataGovUkExport::class,
            Command\DataDvaNiExport::class => CommandHandler\DataDvaNiExport::class,
            Command\CompaniesHouseVsOlcsDiffsExport::class => CommandHandler\CompaniesHouseVsOlcsDiffsExport::class,
            Command\Bus\Expire::class => CommandHandler\Bus\Expire::class,
            Command\Permits\WithdrawUnpaidEcmt::class => CommandHandler\Permits\WithdrawUnpaidEcmt::class,
            Command\ImportUsersFromCsv::class => CommandHandler\ImportUsersFromCsv::class,
            Command\LastTmLetter::class => CommandHandler\LastTmLetter::class,
            Command\Permits\CloseExpiredWindows::class => CommandHandler\Permits\CloseExpiredWindows::class,
        ],
    ],

    \Dvsa\Olcs\Api\Domain\QueryHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            Query\Util\GetDbValue::class => QueryHandler\Util\GetDbValue::class
        ]
    ],

    'batch_config' => [
        'remove-read-audit' => [
            'max-age' => '1 year'
        ],
        'clean-abandoned-variations' => [
            'older-than' => '4 hours'
        ]
    ]
];
