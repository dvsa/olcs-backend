<?php

return [
    'router' => [
        'routes' => [
            'generic' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/[:service][/:id]',
                    'constraints' => [
                        'service' => '[a-z\-]+',
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'Generic'
                    ]
                ]
            ],
            'search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/search/:query[/:index]',
                    'defaults' => [
                        'controller' => 'Search'
                    ]
                ]
            ],
            'ref-data' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/ref-data[/category/:category][/:id]',
                    'defaults' => [
                        'controller' => 'ref-data'
                    ]
                ]
            ],
            'licence-organisation' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/licence-organisation[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'licence-organisation'
                    ]
                ]
            ],
            'operator-search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/operator-search[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'operator-search'
                    ]
                ]
            ],
            'person-search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/person-search[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'person-search'
                    ]
                ]
            ],
            'defendant-search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/defendant-search[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'defendant-search'
                    ]
                ]
            ],
            'organisation-search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/organisation-search[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'organisation-search'
                    ]
                ]
            ],
            'person-licence-search' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/person-licence-search[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'person-licence-search'
                    ]
                ]
            ],
            'trading-names' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/trading-names[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+'
                    ],
                    'defaults' => [
                        'controller' => 'trading-names'
                    ]
                ]
            ],
            'bookmark-search' => [
                'type' => 'literal',
                'options' => [
                    'route' => '/bookmark-search',
                    'defaults' => [
                        'controller' => 'bookmark-search'
                    ]
                ]
            ],
        ]
    ],
    'service_manager' => [
        'shared' => [
            'BundleQuery' => false
        ],
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ],
        'factories' => [
            'serviceFactory' => '\Olcs\Db\Service\Factory',
            'ElasticSearch\Client' => '\Olcs\Db\Service\Search\ClientFactory',
            'ElasticSearch\Search' => '\Olcs\Db\Service\Search\SearchFactory',
            'Olcs\Db\Service\BusReg\OtherServicesManager' =>
                'Olcs\Db\Service\BusReg\OtherServicesManager',
            'Olcs\Db\Service\ContactDetails\PhoneContactsManager' =>
                'Olcs\Db\Service\ContactDetails\PhoneContactsManager'
        ],
        'invokables' => [
            'ExpressionBuilder' => '\Olcs\Db\Utility\ExpressionBuilder',
            'BundleQuery' => '\Olcs\Db\Utility\BundleQuery',
            'PaginateQuery' => '\Olcs\Db\Utility\PaginateQuery'
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Generic' => 'Olcs\Db\Controller\GenericController',
            'Search' => 'Olcs\Db\Controller\SearchController',
            'licencevehicleusage' => 'Olcs\Db\Controller\LicenceVehicleUsageController',
            'licence-vehicle' => 'Olcs\Db\Controller\LicenceVehicleController',
            'note' => 'Olcs\Db\Controller\NoteController',
            'operator-search' => 'Olcs\Db\Controller\OperatorSearchController',
            'person-search' => 'Olcs\Db\Controller\PersonSearchController',
            'person-licence-search' => 'Olcs\Db\Controller\PersonLicenceSearchController',
            'TradingNames' => 'Olcs\Db\Controller\TradingNamesController',
            'defendant-search' => 'Olcs\Db\Controller\DefendantSearchController',
            'organisation-search' => 'Olcs\Db\Controller\OrganisationSearchController',
            'ref-data' => 'Olcs\Db\Controller\RefDataController',
            'bookmark-search' => 'Olcs\Db\Controller\BookmarkSearchController',
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
    ],
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
                    'Olcs\Db\Entity' => 'EntityDriver',
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
                    'yesno' => 'Olcs\Db\Entity\Types\YesNoType',
                    'yesnonull' => 'Olcs\Db\Entity\Types\YesNoNullType',
                    'date' => 'Olcs\Db\Entity\Types\DateType',
                    'datetime' => 'Olcs\Db\Entity\Types\DateTimeType',
                ]
            ]
        ]
    ]
];
