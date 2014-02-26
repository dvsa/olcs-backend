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
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Olcs\Db\Controller\Index',
                    ),
                ),
            ),
            'application' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/application[/:id]',
                    'constraints' => array(
                    'id' => '[0-9]+',
                ),
                'defaults' => array(
                    'controller' => 'Olcs\Db\Controller\Application',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Olcs\Db\Controller\Index' => 'Olcs\Db\Controller\IndexController',
            'Olcs\Db\Controller\Application' => 'Olcs\Db\Controller\ApplicationController',
        ),
    ),
    'view_manager' => array(
        /* 'display_not_found_reason' => false,
        'display_exceptions'       => false, */
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
