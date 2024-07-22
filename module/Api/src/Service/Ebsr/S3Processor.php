<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Olcs\Logging\Log\Logger;

class S3Processor implements EbsrProcessingInterface
{
    private S3Client $s3Client;

    private string $bucketName;

    public function __construct(S3Client $s3Client, string $bucketName)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
    }

    public function process(string $identifier, array $options = []): string
    {
        Logger::info('Sending transxchange file to S3', ['identifier' => basename($identifier)]);

        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key' =>  $options['s3Filename'] ?? basename($identifier),
                'Body' => file_get_contents($identifier)
            ]);

            return $result['ObjectURL'];
        } catch (S3Exception $e) {
            Logger::info('Cannot send transxchange file from content store to s3 ' . $e->getAwsErrorMessage(), ['identifier' => basename($identifier)]);
            throw $e;
        }
    }
}
