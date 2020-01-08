<?php

use Dvsa\Olcs\AwsSdk\Factories\AwsCredentialsProviderFactory;
use Dvsa\Olcs\AwsSdk\Factories\S3ClientFactory;
use Dvsa\Olcs\AwsSdk\Factories\SqsClientFactory;

return [
    'service_manager' => [
        'factories' => [
            'S3Client' => S3ClientFactory::class,
            'AwsCredentialsProvider' => AwsCredentialsProviderFactory::class,
            'SqsClient' => SqsClientFactory::class
        ],
    ]
];
