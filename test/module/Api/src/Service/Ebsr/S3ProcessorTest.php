<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr\ProcessPackException;
use Dvsa\Olcs\Api\Service\Ebsr\S3Processor;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;

class S3ProcessorTest extends TestCase
{
    private S3Processor $sut;

    public function setUp(): void
    {
         parent::setUp();
    }

    /**
     * @throws ProcessPackException
     */
    public function testProcess()
    {
        $mockS3Client = m::mock(S3Client::class);
        $mockBucketName = 'testbucket';
        vfsStream::setup();

        $fileIdentifier = 'testkey';
        $fileContent = 'contents';

        $mockFile = m::mock(\File::class);
        $mockFile->shouldReceive('getContent')->andReturn($fileContent);

        $mockFileUploader = m::mock(FileUploaderInterface::class);
        $mockFileUploader->shouldReceive('download')->with($fileIdentifier)->andReturn($mockFile);
        $mockFileUploader->shouldReceive('remove')->with($fileIdentifier)->andReturn(true);
        $identifier = 'testkey';

        $mockS3Client->shouldReceive('putObject')->once()->with(
            [
                'Bucket' => $mockBucketName,
                'Key' => $identifier,
                'Body' => $fileContent
            ]
        )->andReturn(['ObjectURL' => 'testurl']);

        $mockLogger = m::mock(LoggerInterface::class);
        $mockLogger->shouldReceive('debug')->with('Sending transxchange file to S3', ['identifier' => $fileIdentifier]);
        $sut = new S3Processor($mockS3Client, $mockBucketName, $mockFileUploader, $mockLogger);
        $this->assertEquals('testurl', $sut->process($fileIdentifier, []));
    }

    public function testException()
    {
        $this->expectException(ProcessPackException::class);
        $mockS3Client = m::mock(S3Client::class);
        $mockS3Client->shouldReceive('putObject')->andThrow(new S3Exception('test', m::mock(\Aws\Command::class)));
        $mockBucketName = 'testbucket';
        vfsStream::setup();

        $fileIdentifier = 'testkey';
        $fileContent = 'contents';

        $mockFile = m::mock(\File::class);
        $mockFile->shouldReceive('getContent')->andReturn($fileContent);

        $mockFileUploader = m::mock(FileUploaderInterface::class);
        $mockFileUploader->shouldReceive('download')->with($fileIdentifier)->andReturn($mockFile);
        $mockLogger = m::mock(LoggerInterface::class);
        $mockLogger->shouldReceive('debug')->with('Sending transxchange file to S3', ['identifier' => $fileIdentifier]);
        $mockLogger->shouldReceive('info')->with('Cannot send transxchange file from content store to s3 ', ['identifier' => $fileIdentifier]);
        $sut = new S3Processor($mockS3Client, $mockBucketName, $mockFileUploader, $mockLogger);
        $sut->process($fileIdentifier, []);
    }
}
