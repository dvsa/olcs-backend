<?php

return [
    'service_manager' => [
        'factories' => [
            \Dvsa\Olcs\Address\Service\Address::class => \Dvsa\Olcs\Address\Service\AddressFactory::class
        ],
        'aliases' => [
            'AddressService' => \Dvsa\Olcs\Address\Service\Address::class
        ],
    ],
];
