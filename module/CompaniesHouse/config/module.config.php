<?php

return [
    'companies_house' => [
        'http' => [
            'sslcapath' => '/etc/ssl/certs',
            'sslverifypeer' => true,
        ],
        'auth' => [
            'username' => 'changeme',
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
