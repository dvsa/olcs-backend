<?php

return [
    'email' => [
        'http' => [],
        'client' => [
            'baseuri' => 'http://olcs-email/',
            'from_name' => 'OLCS do not reply',
            'from_email' => 'donotreply@otc.gsi.gov.uk',
            'selfserve_uri' => 'http://olcs-selfserve/',
        ]
    ],
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\Email\Service\Client::class => \Dvsa\Olcs\Email\Service\ClientFactory::class,
            \Dvsa\Olcs\Email\Service\TemplateRenderer::class => \Dvsa\Olcs\Email\Service\TemplateRendererFactory::class,
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'layout/email' => __DIR__ . '/../view/layout/email.phtml',
        ],
        'template_path_stack' => [
            'email' => __DIR__ . '/../view/email',
        ]
    ],
];
