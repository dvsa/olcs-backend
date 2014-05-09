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
            )
        )
    ),
    'companies_house_credentials' => array(
        'password' => 'XMLGatewayTestPassword',
        'userId'   => 'XMLGatewayTestUserID'
    )
);
