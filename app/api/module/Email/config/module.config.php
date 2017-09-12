<?php

use Dvsa\Olcs\Email\Domain\Command;
use Dvsa\Olcs\Email\Domain\CommandHandler;

return [
    'email' => [
        'from_name' => 'OLCS do not reply',
        'from_email' => 'donotreply@otc.gsi.gov.uk',
        'selfserve_uri' => 'http://olcs-selfserve/',
        'internal_uri' => 'http://olcs-internal/',
    ],
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\Email\Service\TemplateRenderer::class => \Dvsa\Olcs\Email\Service\TemplateRendererFactory::class,
            'EmailService' => \Dvsa\Olcs\Email\Service\Email::class,
            'ImapService' => \Dvsa\Olcs\Email\Service\Imap::class,
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'emailStyle' => \Dvsa\Olcs\Email\View\Helper\EmailStyle::class,
        ]
    ],
    'view_manager' => [
        'template_path_stack' => [
            'layout' => __DIR__ . '/../view/layout',
            'email' => __DIR__ . '/../view/email',
        ]
    ],
    \Dvsa\Olcs\Api\Domain\CommandHandlerManagerFactory::CONFIG_KEY => [
        'factories' => [
            Command\SendEmail::class => CommandHandler\SendEmail::class,
            Command\ProcessInspectionRequestEmail::class => CommandHandler\ProcessInspectionRequestEmail::class,
            Command\UpdateInspectionRequest::class => CommandHandler\UpdateInspectionRequest::class,
        ]
    ],
];
