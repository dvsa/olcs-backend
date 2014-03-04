<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'doctrine' => array(
        'driver' => array(
            'Olcs_Db_Driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(realpath(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR. 'src/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Olcs\Db\Entity' => 'Olcs_Db_Driver'
                )
            )
        )
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Index',
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
                        'controller' => 'Application',
                    ),
                ),
            ),
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user[/:id][/:action]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User',
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
        'factories' => array(
            'User' => function ($serviceManager) {
                $s = new \Olcs\Db\Service\User();
                $s->setEntityManager($serviceManager->get('doctrine.entitymanager.orm_default'));
                $s->setServiceLocator($serviceManager);
                return $s;
            }
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Index'       => 'Olcs\Db\Controller\IndexController',
            'Application' => 'Olcs\Db\Controller\ApplicationController',
            'User'        => 'Olcs\Db\Controller\UserController',
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
