<?php

namespace Dvsa\OlcsTest\Email\Transport;

use Dvsa\Olcs\Email\Transport\MultiTransportOptions;
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
        $sut = new MultiTransportOptions();

        $this->assertSame([], $sut->getTransport());

        $sut->setTransport([['type' => 'File'], ['type' => 'SendMail']]);

        $transports = $sut->getTransport();
        $this->assertInstanceOf(File::class, $transports[0]);
        $this->assertInstanceOf(Sendmail::class, $transports[1]);
    }
}
