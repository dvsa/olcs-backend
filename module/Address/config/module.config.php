<?php

return [
    'address' => [
        'client' => [
            'baseuri' => 'http://postcode.cit.olcs.mgt.mtpdvsa/'
        ]
    ],
    'service_manager' => [
        'factories' => [
            'AddressService' => \Dvsa\Olcs\Address\Service\AddressFactory::class
        ],
    ],
];
