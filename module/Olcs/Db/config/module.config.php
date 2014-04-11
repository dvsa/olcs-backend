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
                        'service' => '[a-zA-Z\-]+',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Generic'
                    )
                )
            ),
            'rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:controller][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z\-]+',
                        'id' => '[0-9]+'
                    )
                )
            )
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
            'operator-search' => 'Olcs\Db\Controller\OperatorSearchController',
            'person-search' => 'Olcs\Db\Controller\PersonSearchController',
            'person-licence-search' => 'Olcs\Db\Controller\PersonLicenceSearchController'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => false,
        'strategies' => array(
            'ViewJsonStrategy'
        )
    )
);
