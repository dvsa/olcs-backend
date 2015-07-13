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
                // @TODO make this a queue consumer, this is just for testing
                'ch-initial-load' => [
                    'options' => [
                        'route' => 'ch-initial-load [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'BatchController',
                            'action' => 'initialLoad',
                        ],
                    ],
                ],
                'process-queue' => [
                    'options' => [
                        'route' => 'process-queue [<type>]',
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
            'que_typ_ch_initial' => Dvsa\Olcs\Cli\Service\Queue\CompaniesHouse\InitialDataLoad::class,
            'que_typ_ch_compare' => Dvsa\Olcs\Cli\Service\Queue\CompaniesHouse\Compare::class,
        ]
    ],
    'queue' => [
        // 'isLongRunningProcess' => true,
        'runFor' => 60
    ],
];
