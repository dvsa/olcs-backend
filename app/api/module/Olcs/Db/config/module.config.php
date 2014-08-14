<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
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
            'ref-data' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/ref-data/:id[/:lang]',
                    'defaults' => [
                        'controller' => 'ref-data',
                        'lang' => 'en_en'
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
        ]
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ],
        'factories' => [
            'serviceFactory' => '\Olcs\Db\Service\Factory'
        ]
    ],
    'controllers' => [
        'invokables' => [
            'Generic' => 'Olcs\Db\Controller\GenericController',
            'licencevehicleusage' => 'Olcs\Db\Controller\LicenceVehicleUsageController',
            'licence-vehicle' => 'Olcs\Db\Controller\LicenceVehicleController',
            'note' => 'Olcs\Db\Controller\NoteController',
            'operator-search' => 'Olcs\Db\Controller\OperatorSearchController',
            'person-search' => 'Olcs\Db\Controller\PersonSearchController',
            'person-licence-search' => 'Olcs\Db\Controller\PersonLicenceSearchController',
            'TradingNames' => 'Olcs\Db\Controller\TradingNamesController',
            'ref-data' => 'Olcs\Db\Controller\RefDataController'
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
    'companies_house_credentials' => [
        'password' => 'XMLGatewayTestPassword',
        'userId'   => 'XMLGatewayTestUserID'
    ],
    'doctrine' => [
        'driver' => [
            'translatable_metadata_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    'vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Gedmo\Translatable\Entity' => 'translatable_metadata_driver'
                ],
            ],
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
                ]
            ],
        ]
    ]
];
