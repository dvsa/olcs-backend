<?php

return [
    'companies_house' => [
        'http' => [
            // 'options' => [
                'sslcapath' => '/etc/ssl/certs',
                'sslverifypeer' => false,
            // ],
        ],
        'auth' => [
            'username' => 'JAuzjQG4JIv2XNB0mz1ID8Ut6QHEzSLYR3bkzGP9',
            'password' => '',
        ],
        'client' => [
            'baseuri' => 'https://api.companieshouse.gov.uk/',
        ],
    ],
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\CompaniesHouse\Service\Client::class => \Dvsa\Olcs\CompaniesHouse\Service\ClientFactory::class,
        ],
    ],
];
