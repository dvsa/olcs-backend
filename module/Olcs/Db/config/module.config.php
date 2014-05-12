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
            'licence-vehicle' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/licence-vehicle[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'licence-vehicle'
                    )
                )
            ),
            'organisation-application' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/organisation-application[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'organisation-application'
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
            'licence-organisation' => 'Olcs\Db\Controller\LicenceOrganisationController',
            'licencevehicleusage' => 'Olcs\Db\Controller\LicenceVehicleUsageController',
            'licence-vehicle' => 'Olcs\Db\Controller\LicenceVehicleController',
            'note' => 'Olcs\Db\Controller\NoteController',
            'operator-search' => 'Olcs\Db\Controller\OperatorSearchController',
            'person-search' => 'Olcs\Db\Controller\PersonSearchController',
            'person-licence-search' => 'Olcs\Db\Controller\PersonLicenceSearchController',
            'OrganisationApplication' => 'Olcs\Db\Controller\OrganisationApplicationController'
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
    )
);
