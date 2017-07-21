<?php

namespace Dvsa\OlcsTest\Email\Transport;

use Dvsa\Olcs\Email\Transport\MultiTransport;
use Dvsa\Olcs\Email\Transport\S3File;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mail\Transport\File;
use Zend\Mail\Transport\Sendmail;
use Dvsa\Olcs\Email\Transport\Factory;

/**
 * Class FactoryTest
 */
class FactoryTest extends MockeryTestCase
{
    public function testMultiTransport()
    {
        $transport = Factory::create(
            [
                'type' => 'Dvsa\Olcs\Email\Transport\MultiTransport',
                'options' => [
                    'transport' => [
                        ['type' => 'File'],
                        ['type' => 'SendMail'],
                    ]
                ]
            ]
        );

        $this->assertInstanceOf(MultiTransport::class, $transport);
        $transports = $transport->getOptions()->getTransport();
        $this->assertCount(2, $transports);
        $this->assertInstanceOf(File::class, $transports[0]);
        $this->assertInstanceOf(Sendmail::class, $transports[1]);
    }

    public function testS3File()
    {
        $transport = Factory::create(
            [
                'type' => 'Dvsa\Olcs\Email\Transport\S3File',
                'options' => [
                    's3Path' => 'PATH',
                ]
            ]
        );

        $this->assertInstanceOf(S3File::class, $transport);
        $this->assertSame('PATH', $transport->getOptions()->getS3Path());
    }
}
