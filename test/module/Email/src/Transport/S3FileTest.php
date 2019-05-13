<?php

namespace Dvsa\OlcsTest\Email\Transport;

use Aws\Command;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Email\Transport\MultiTransport;
use Dvsa\Olcs\Email\Transport\MultiTransportOptions;
use Dvsa\Olcs\Email\Transport\S3File;
use Dvsa\Olcs\Email\Transport\S3FileOptions;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mail\Message;
use Zend\Mail\Transport\Exception\RuntimeException;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\Sendmail;
use Mockery as m;

/**
 * Class S3FileTest
 */
class S3FileTest extends MockeryTestCase
{
    public function testSendSuccess()
    {

        $mockMessage = m::mock(Message::class);
        $mockMessage->shouldReceive('getSubject')->with()->once()->andReturn('__-TEST %$£%SUBJECT*&&^');

        $mockFileTransport = m::mock(File::class);
        $mockFileTransport->shouldReceive('send')->with($mockMessage)->once();
        $mockFileTransport->shouldReceive('getLastFile')->with()->once()->andReturn('EMAIL_FILE');
        $mockS3Client = m::mock(S3Client::class);
        $mockS3Client->shouldReceive('putObject')->once()->with([
            'Bucket' => 'testBucket',
            'Key' => 'TEST_SUBJECT',
            'SourceFile' => 'EMAIL_FILE'
        ])->andReturnSelf();
        $mockOptions = m::mock(S3FileOptions::class);
        $mockOptions->shouldReceive('getS3Client')->andReturn($mockS3Client);
        $mockOptions->shouldReceive('getS3Bucket')->andReturn('testBucket');
        $mockOptions->shouldReceive('getAwsOptions')->andReturn(['region' => 'test', "version" => 'latest']);

        $sut = m::mock(S3File::class, [$mockFileTransport])->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->setOptions($mockOptions);


        $sut->shouldReceive('deleteFile')->with('EMAIL_FILE')->once();

        $sut->send($mockMessage);
    }

    public function testSendFail()
    {
        $mockMessage = m::mock(Message::class);
        $mockMessage->shouldReceive('getSubject')->with()->once()->andReturn('__-TEST %$£%SUBJECT*&&^');

        $mockFileTransport = m::mock(File::class);
        $mockFileTransport->shouldReceive('send')->with($mockMessage)->once();
        $mockFileTransport->shouldReceive('getLastFile')->with()->once()->andReturn('EMAIL_FILE');

        $mockS3Client = m::mock(S3Client::class);
        $mockS3Client->shouldReceive('putObject')->once()->with(
            [
                'Bucket' => 'testBucket',
                'Key' => 'TEST_SUBJECT',
                'SourceFile' => 'EMAIL_FILE'
            ]
        )->andThrow(
            new S3Exception('test', new Command('test'))
        );
        $mockOptions = m::mock(S3FileOptions::class);
        $mockOptions->shouldReceive('getS3Client')->andReturn($mockS3Client);
        $mockOptions->shouldReceive('getS3Bucket')->andReturn('testBucket');
        $mockOptions->shouldReceive('getS3Key')->andReturn('testKey');
        $mockOptions->shouldReceive('getAwsOptions')->andReturn(['region' => 'test', "version" => 'latest']);


        $sut = m::mock(S3File::class, [$mockFileTransport])->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->setOptions($mockOptions);

        $sut->shouldReceive('deleteFile')->with('EMAIL_FILE')->once();

        $this->expectException(RuntimeException::class, "Cannot send mail to S3 : OUTPUT 1");

        $sut->send($mockMessage);
    }
}
