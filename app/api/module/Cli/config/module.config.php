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
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'BatchController' => Dvsa\Olcs\Cli\Controller\BatchController::class,
        ]
    ],
    'cache' => [
        'adapter' => [
            // apc_cli is not currently enabled in environments therefore change it
            'name' => 'memory',
        ]
    ],
];
