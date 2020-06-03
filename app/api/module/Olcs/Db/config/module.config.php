<?php

return [
    'router' => [
        'routes' => [
            'search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/search/:index',
                    'defaults' => [
                        'controller' => 'Search'
                    ]
                ]
            ],
        ]
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Log\LoggerAbstractServiceFactory'
        ],
        'factories' => [
            'ElasticSearch\Client' => '\Olcs\Db\Service\Search\ClientFactory',
            'ElasticSearch\Search' => '\Olcs\Db\Service\Search\SearchFactory',
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Search' => Olcs\Db\Controller\SearchController::class,
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => false,
        'strategies' => [
            'ViewJsonStrategy'
        ],
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
             'error/404' => __DIR__ . '/../view/error/404.phtml',
             'error/index' => __DIR__ . '/../view/error/index.phtml',
             'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
         ],
    ]
];
