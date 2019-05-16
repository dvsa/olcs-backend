<?php


return [
    'service_manager' => [
        'factories' => [
            'S3FileOptions' =>\Dvsa\Olcs\Email\Transport\S3FileOptionsFactory::class,
            'S3Client' =>\Dvsa\Olcs\AwsSdk\S3ClientFactory::class,
            'AwsCredentialsProvider' => \Dvsa\Olcs\AwsSdk\AwsCredentailsProviderFactory::class
            ],
    ]
];
