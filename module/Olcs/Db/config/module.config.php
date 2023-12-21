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
            'Laminas\Log\LoggerAbstractServiceFactory'
        ],
        'factories' => [
            'Elasticsearch\Client' => '\Olcs\Db\Service\Search\ClientFactory',
            'Elasticsearch\Search' => '\Olcs\Db\Service\Search\SearchFactory',
        ],
    ],
    'controllers' => [
        'invokables' => [

        ],
        'factories' => [
            'Search' => Olcs\Db\Controller\SearchControllerFactory::class,
        ],
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
