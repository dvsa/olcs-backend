<?php
use Dvsa\Olcs\GdsVerify;

return array(
    'service_manager' => [
        'factories' => [
            GdsVerify\Service\GdsVerify::class => GdsVerify\Service\GdsVerify::class
        ]
    ],
    'gds_verify' => [
        // URL of hub metadata
        'federation_metadata_url' => __DIR__ .'/../data/compliance-tool/federation.xml',
        // URL of Matching Service Adapter metadata
        'msa_metadata_url' => __DIR__ .'/../data/compliance-tool/msa-certs/metadata.xml',
        // Cache settings used to cache the above two metadata documents
        //'cache' => [
        //    'adapter' => [
        //        'name'    => 'filesystem',
        //        'options' => array('ttl' => 300),
        //    ],
        //],
        // Entity identifier
        'entity_identifier' => 'http://olcs-selfserve.olcs.gov.uk',
        // Key used to sign authentication requests
        'signature_key' => __DIR__ .'/../data/compliance-tool/signing.key',
        // Key used to decrypt data from hub
        'encryption_keys' => [
            __DIR__ .'/../data/compliance-tool/signing.key',
//            __DIR__ .'/../data/compliance-tool/enc.key',
        ],
    ]
);
