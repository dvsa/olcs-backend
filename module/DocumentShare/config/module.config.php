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
            Dvsa\Olcs\DocumentShare\Service\Client::class => Dvsa\Olcs\DocumentShare\Service\ClientFactory::class,
        ]
    ]
];
