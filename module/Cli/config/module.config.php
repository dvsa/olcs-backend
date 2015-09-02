<?php

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
                'process-queue' => [
                    'options' => [
                        'route' => 'process-queue [--type=]',
                        'defaults' => [
                            'controller' => 'QueueController',
                            'action' => 'index'
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
            'que_typ_ch_initial' => Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse\InitialDataLoad::class,
            'que_typ_ch_compare' => Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse\Compare::class,
            'que_typ_cont_checklist' => Dvsa\Olcs\Cli\Service\Queue\Consumer\ContinuationChecklist::class,
        ],
        'factories' => [
            'que_typ_cpid_export_csv'
                => Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory\CpidOrganisationExportFactory::class,
        ]
    ],
    'queue' => [
        //'isLongRunningProcess' => true,
        'runFor' => 60
    ],
    'file-system' => [
        'path' => '/tmp'
    ]
];
