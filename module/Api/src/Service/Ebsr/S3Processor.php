<?php

namespace Dvsa\Olcs\Api\Service\Ebsr;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\ProcessPackException;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use Psr\Log\LoggerInterface;


class S3Processor implements EbsrProcessingInterface
{
    const OUTPUT_TYPE = 's3Filename';

    private S3Client $s3Client;

    private string $bucketName;

    private FileUploaderInterface $fileUploader;
    private LoggerInterface $logger;

    public function __construct(S3Client $s3Client, string $bucketName, FileUploaderInterface $fileUploader, LoggerInterface $logger)
    {
        $this->s3Client = $s3Client;
        $this->bucketName = $bucketName;
        $this->fileUploader = $fileUploader;
        $this->logger = $logger;
    }

    /**
     * @param string $identifier
     * @param array $options
     * @param string $fileName
     * @return void
     * @throws ProcessPackException
     */
    public function process(string $identifier, array $options = []): string
    {

        $this->logger->debug('Sending transxchange file to S3', ['identifier' => $identifier]);
        $file = $this->fileUploader->download($identifier);
        if (!$file) {
            $this->logger->info('Cannot get transxchange file from content store', ['identifier' => $identifier]);
            throw new ProcessPackException('Cannot process transxchange file');
        }
        $fileContent = $file->getContent();


        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key' =>  basename($identifier),
                'Body' => $fileContent
            ]);
            $this->fileUploader->remove($identifier);
            return $result['ObjectURL'];
        } catch (S3Exception $e) {
            $this->logger->info('Cannot send transxchange file from content store to s3 '. $e->getAwsErrorMessage(), ['identifier' => $identifier]);
            throw new ProcessPackException('Cannot process transxchange file');

        }
    }

    public function getOutputType(): string
    {
        return self::OUTPUT_TYPE;
    }
}
