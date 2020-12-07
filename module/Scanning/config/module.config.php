<?php

return array(
    'router' => array(
        'routes' => array(
            'document' => array(
                'type' => 'Laminas\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/scanning/document',
                    'defaults' => array(
                        'controller' => Dvsa\Olcs\Scanning\Controller\DocumentController::class,
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(),
    ),
    'controllers' => array(
        'invokables' => array(
            Dvsa\Olcs\Scanning\Controller\DocumentController::class =>
                Dvsa\Olcs\Scanning\Controller\DocumentController::class
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => false,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/error',
        'exception_template'       => 'error/error',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/error'               => __DIR__ . '/../view/error/error.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    )
);
