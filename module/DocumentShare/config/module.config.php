<?php

return [
    'document_share' => [
        'http' => [],
        'client' => [
            'baseuri' => '',
            'workspace' => '',
            'username' => '',
            'password' => ''
        ]
    ],
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\DocumentShare\Service\WebDavClient::class => Dvsa\Olcs\DocumentShare\Service\ClientFactory::class,
            \Dvsa\Olcs\DocumentShare\Service\DocManClient::class => \Dvsa\Olcs\DocumentShare\Service\ClientFactory::class
        ]
    ]
];

