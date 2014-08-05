<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'generic' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:service][/:id]',
                    'constraints' => array(
                        'service' => '[a-z\-]+',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Generic'
                    )
                )
            ),
            'licence-organisation' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/licence-organisation[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'licence-organisation'
                    )
                )
            ),
            'operator-search' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/operator-search[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'operator-search'
                    )
                )
            ),
            'person-search' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/person-search[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'person-search'
                    )
                )
            ),
            'person-licence-search' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/person-licence-search[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'person-licence-search'
                    )
                )
            ),
            'trading-names' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/trading-names[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'trading-names'
                    )
                )
            ),
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory'
        ),
        'factories' => array(
            'serviceFactory' => '\Olcs\Db\Service\Factory'
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'Generic' => 'Olcs\Db\Controller\GenericController',
            'licencevehicleusage' => 'Olcs\Db\Controller\LicenceVehicleUsageController',
            'licence-vehicle' => 'Olcs\Db\Controller\LicenceVehicleController',
            'note' => 'Olcs\Db\Controller\NoteController',
            'operator-search' => 'Olcs\Db\Controller\OperatorSearchController',
            'person-search' => 'Olcs\Db\Controller\PersonSearchController',
            'person-licence-search' => 'Olcs\Db\Controller\PersonLicenceSearchController',
            'TradingNames' => 'Olcs\Db\Controller\TradingNamesController',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => false,
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
             'error/404' => __DIR__ . '/../view/error/404.phtml',
             'error/index' => __DIR__ . '/../view/error/index.phtml',
             'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
         ),
    ),
    'companies_house_credentials' => array(
        'password' => 'XMLGatewayTestPassword',
        'userId'   => 'XMLGatewayTestUserID'
    ),
    'doctrine' => [
        'driver' => [
            'EntityDriver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    'Olcs\Db\Entity' => 'EntityDriver'
                ]
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'yesno' => 'Olcs\Db\Entity\Types\YesNoType',
                    'yesnonull' => 'Olcs\Db\Entity\Types\YesNoNullType',
                ]
            ]
        ]
    ]
);
