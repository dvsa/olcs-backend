<?php


use Dvsa\Olcs\AwsSdk\Factories\AwsCredentialsProviderFactory;
use Dvsa\Olcs\AwsSdk\Factories\S3ClientFactory;

return [
    'service_manager' => [
        'factories' => [
            'S3FileOptions' =>\Dvsa\Olcs\Email\Transport\S3FileOptionsFactory::class,
            'S3Client' => S3ClientFactory::class,
            'AwsCredentialsProvider' => AwsCredentialsProviderFactory::class
        ],
    ]
];
