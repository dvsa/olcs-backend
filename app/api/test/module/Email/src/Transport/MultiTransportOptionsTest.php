<?php

namespace Dvsa\OlcsTest\Email\Transport;

use Aws\S3\S3Client;
use Dvsa\Olcs\Email\Transport\MultiTransportOptions;
use Dvsa\Olcs\Email\Transport\S3FileOptions;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\Sendmail;

/**
 * Class MultiTransportOptionsTest
 */
class MultiTransportOptionsTest extends MockeryTestCase
{
    public function testSetGet()
    {
        $sut = new MultiTransportOptions([], new S3FileOptions([], new S3Client([
            'region'=>'eu-west-1',
            'version' =>'latest'
        ])));

        $this->assertSame([], $sut->getTransport());

        $sut->setTransport([['type' => 'File'], ['type' => 'SendMail']]);

        $transports = $sut->getTransport();
        $this->assertInstanceOf(File::class, $transports[0]);
        $this->assertInstanceOf(Sendmail::class, $transports[1]);
    }
}
