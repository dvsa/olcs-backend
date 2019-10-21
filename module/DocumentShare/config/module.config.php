<?php

return [
    'document_share' => [
        'http' => [],
        'client' => [
            'baseuri' => '',
            'workspace' => '',
        ]
    ],
    'service_manager' => [
        'factories' => [
            Dvsa\Olcs\DocumentShare\Service\Client::class => Dvsa\Olcs\DocumentShare\Service\ClientFactory::class,
        ]
    ]
];
