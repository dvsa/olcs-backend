<?php

return [
    'document_share' => [
        'http' => [],
        'client' => [
            'baseuri' => '',
            'workspace' => ''
        ]
    ],
    'service_manager' => [
        'factories' => [
            'Dvsa\Olcs\DocumentShare\Service\Client' => 'Dvsa\Olcs\DocumentShare\Service\ClientFactory',
        ]
    ]
];
