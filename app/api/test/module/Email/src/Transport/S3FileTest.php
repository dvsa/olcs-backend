<?php

namespace Dvsa\OlcsTest\Email\Transport;

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

        $mockOptions = m::mock(S3FileOptions::class);
        $mockOptions->shouldReceive('getS3Path')->with()->once()->andReturn('S3_PATH');

        $sut = m::mock(S3File::class, [$mockFileTransport])->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->setOptions($mockOptions);
        $sut->shouldReceive('executeCommand')->once()->andReturnUsing(
            function ($command, &$output, &$result) {
                $s3FileName = 'TEST_SUBJECT';
                $this->assertSame('aws s3 cp EMAIL_FILE s3://S3_PATH/'.$s3FileName.' 2>&1', $command);
                $output = [];
                $result = 0;
            }
        );
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

        $mockOptions = m::mock(S3FileOptions::class);
        $mockOptions->shouldReceive('getS3Path')->with()->once()->andReturn('S3_PATH');

        $sut = m::mock(S3File::class, [$mockFileTransport])->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->setOptions($mockOptions);
        $sut->shouldReceive('executeCommand')->once()->andReturnUsing(
            function ($command, &$output, &$result) {
                $s3FileName = 'TEST_SUBJECT';
                $this->assertSame('aws s3 cp EMAIL_FILE s3://S3_PATH/'.$s3FileName.' 2>&1', $command);
                $output = ['OUTPUT 1'];
                $result = 67;
            }
        );
        $sut->shouldReceive('deleteFile')->with('EMAIL_FILE')->once();

        $this->expectException(RuntimeException::class, "Cannot send mail to S3 : OUTPUT 1");

        $sut->send($mockMessage);
    }
}
