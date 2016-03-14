<?php

use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Cli\Domain\CommandHandler;
use Dvsa\Olcs\Cli\Domain\Command;

return [
    'console' => [
        'router' => [
            'routes' => [
                'licence-status-rules' => [
                    'options' => [
                        'route' => 'licence-status-rules [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'licenceStatusRules'
                        ],
                    ],
                ],
                'enqueue-ch-compare' => [
                    'options' => [
                        'route' => 'enqueue-ch-compare [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'enqueueCompaniesHouseCompare',
                        ],
                    ],
                ],
                'duplicate-vehicle-warning' => [
                    'options' => [
                        'route' => 'duplicate-vehicle-warning [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'duplicateVehicleWarning',
                        ],
                    ],
                ],
                'batch-cns' => [
                    'options' => [
                        'route' => 'batch-cns [--verbose|-v] [--dryrun|-d]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'continuationNotSought'
                        ],
                    ],
                ],
                'process-queue' => [
                    'options' => [
                        'route' => 'process-queue [--type=]',
                        'defaults' => [
                            'controller' => 'QueueController',
                            'action' => 'index'
                        ],
                    ],
                ],
                'process-inbox' => [
                    'options' => [
                        'route' => 'process-inbox [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'processInboxDocuments'
                        ],
                    ],
                ],
                'process-ntu' => [
                    'options' => [
                        'route' => 'process-ntu [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'processNtu'
                        ],
                    ],
                ],
                'inspection-request-email' => [
                    'options' => [
                        'route' => 'inspection-request-email [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'inspectionRequestEmail'
                        ],
                    ],
                ],
                'remove-read-audit' => [
                    'options' => [
                        'route' => 'remove-read-audit [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'removeReadAudit'
                        ],
                    ],
                ],
                'system-parameter' => [
                    'options' => [
                        'route' => 'system-parameter <name> <value> [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'setSystemParameter'
                        ],
                    ],
                ],
                'resolve-payments' => [
                    'options' => [
                        'route' => 'resolve-payments [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
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
                            'controller' => 'BatchController',
                            'action' => 'createViExtractFiles',
                        ],
                    ],
                ],
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'BatchController' => Dvsa\Olcs\Cli\Controller\BatchController::class,
            'QueueController' => Dvsa\Olcs\Cli\Controller\QueueController::class,
        ]
    ],
    'cache' => [
        'adapter' => [
            // apc_cli is not currently enabled in environments therefore change it
            'name' => 'memory',
        ]
    ],
    'service_manager' => [
        'invokables' => [
            'Queue' => Dvsa\Olcs\Cli\Service\Queue\QueueProcessor::class,
        ],
        'factories' => [
            'MessageConsumerManager' => Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManagerFactory::class,
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
            Queue::TYPE_EMAIL
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Email\Send::class,
            Queue::TYPE_PRINT
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\PrintJob\PrintJob::class,
            Queue::TYPE_DISC_PRINTING
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\DiscPrinting\PrintDiscs::class,
            Queue::TYPE_CREATE_GOODS_VEHICLE_LIST
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\DiscPrinting\CreateGoodsVehicleList::class,
            Queue::TYPE_CREATE_PSV_VEHICLE_LIST
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\DiscPrinting\CreatePsvVehicleList::class,
        ],
        'factories' => [
            Queue::TYPE_CPID_EXPORT_CSV
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory\CpidOrganisationExportFactory::class,
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
            Command\CreateViExtractFiles::class => CommandHandler\CreateViExtractFiles::class,
        ]
    ],
    'batch_config' => [
        'remove-read-audit' => [
            'max-age' => '1 year'
        ]
    ]
];
